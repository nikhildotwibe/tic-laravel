<?php

namespace Modules\Quotations\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Quotations\Entities\Itinerary;
use Modules\Quotations\Entities\ItineraryEntry;

class CleanupOldVersions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotations:cleanup-old-versions
                            {--dry-run : Preview what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Soft-delete old itinerary versions for enquiries whose end date has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $today = Carbon::today()->toDateString();

        if ($isDryRun) {
            $this->info('=== DRY RUN MODE — nothing will be deleted ===');
        }

        $this->info("Running cleanup for enquiries with end_date < {$today}...");

        // Step 1: Find all enquiry IDs that have itineraries whose end_date is in the past
        // and have more than 1 version (no point cleaning up single-version enquiries)
        $enquiryIds = Itinerary::query()
            ->whereNull('deleted_at')
            ->where('end_date', '<', $today)
            ->groupBy('enquiry_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('enquiry_id')
            ->filter()
            ->values();

        if ($enquiryIds->isEmpty()) {
            $this->info('No eligible enquiries found. Nothing to clean up.');
            return self::SUCCESS;
        }

        $this->info("Found {$enquiryIds->count()} enquiry(ies) with past end dates and multiple versions.");

        $totalDeleted = 0;
        $totalEntriesDeleted = 0;
        $errors = 0;

        foreach ($enquiryIds as $enquiryId) {
            try {
                [$deletedVersions, $deletedEntries] = $this->cleanupEnquiry($enquiryId, $isDryRun);
                $totalDeleted += $deletedVersions;
                $totalEntriesDeleted += $deletedEntries;
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error processing enquiry {$enquiryId}: {$e->getMessage()}");
                Log::error("CleanupOldVersions: Error processing enquiry {$enquiryId}", [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $action = $isDryRun ? 'Would soft-delete' : 'Soft-deleted';
        $this->info("---");
        $this->info("{$action} {$totalDeleted} version(s) and {$totalEntriesDeleted} entrie(s) across {$enquiryIds->count()} enquiry(ies).");

        if ($errors > 0) {
            $this->warn("{$errors} enquiry(ies) had errors — check logs for details.");
        }

        Log::info("CleanupOldVersions: {$action} {$totalDeleted} versions, {$totalEntriesDeleted} entries. Errors: {$errors}");

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Clean up old versions for a single enquiry.
     *
     * @return array [deletedVersions, deletedEntries]
     */
    private function cleanupEnquiry(string $enquiryId, bool $isDryRun): array
    {
        $deletedVersions = 0;
        $deletedEntries = 0;

        // Load all non-soft-deleted itineraries for this enquiry
        $allItineraries = Itinerary::query()
            ->where('enquiry_id', $enquiryId)
            ->whereNull('deleted_at')
            ->orderBy('version', 'asc')
            ->get();

        // Group by parent_itinerary_id (each group is a version chain)
        $groups = $allItineraries->groupBy('parent_itinerary_id');

        foreach ($groups as $parentId => $versions) {
            // Skip groups with 2 or fewer versions — nothing worth cleaning
            if ($versions->count() <= 2) {
                continue;
            }

            // Determine which IDs to KEEP:
            $keepIds = collect();

            // 1. Keep ALL confirmed versions (booking_status = 'confirmed')
            $confirmedIds = $versions->where('booking_status', 'confirmed')->pluck('id');
            $keepIds = $keepIds->merge($confirmedIds);

            // 2. Keep the current version (is_current = true)
            $currentIds = $versions->where('is_current', true)->pluck('id');
            $keepIds = $keepIds->merge($currentIds);

            // 3. Keep 1 latest non-confirmed, non-current version
            //    (the most recent by version number)
            $nonConfirmedNonCurrent = $versions->filter(function ($v) {
                return $v->booking_status !== 'confirmed' && !$v->is_current;
            })->sortByDesc('version');

            if ($nonConfirmedNonCurrent->isNotEmpty()) {
                $keepIds->push($nonConfirmedNonCurrent->first()->id);
            }

            $keepIds = $keepIds->unique();

            // Determine which to DELETE
            $toDelete = $versions->filter(function ($v) use ($keepIds) {
                return !$keepIds->contains($v->id);
            });

            if ($toDelete->isEmpty()) {
                continue;
            }

            $deleteIds = $toDelete->pluck('id')->toArray();

            if ($isDryRun) {
                foreach ($toDelete as $v) {
                    $this->line("  [DRY RUN] Would delete: Enquiry={$enquiryId} Parent={$parentId} Version=v{$v->version} Status={$v->booking_status} ID={$v->id}");
                }
                $entryCount = ItineraryEntry::whereIn('itinerary_id', $deleteIds)->whereNull('deleted_at')->count();
                $deletedVersions += count($deleteIds);
                $deletedEntries += $entryCount;
            } else {
                DB::beginTransaction();
                try {
                    // Soft-delete entries first, then itineraries
                    $entryCount = ItineraryEntry::whereIn('itinerary_id', $deleteIds)
                        ->whereNull('deleted_at')
                        ->count();

                    ItineraryEntry::whereIn('itinerary_id', $deleteIds)->delete();
                    Itinerary::whereIn('id', $deleteIds)->delete();

                    DB::commit();

                    foreach ($toDelete as $v) {
                        $this->line("  Deleted: Enquiry={$enquiryId} Parent={$parentId} Version=v{$v->version} Status={$v->booking_status} ID={$v->id}");
                    }

                    $deletedVersions += count($deleteIds);
                    $deletedEntries += $entryCount;
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        }

        return [$deletedVersions, $deletedEntries];
    }
}
