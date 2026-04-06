<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Entities\User;

class Enquiry extends BaseModel
{
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }

    // public function sub_destination(): BelongsTo
    // {
    //     return $this->belongsTo(SubDestination::class, 'sub_destination_id', 'id');
    // }

    public function sub_destinations(): BelongsToMany
    {
        return $this->belongsToMany(SubDestination::class, EnquirySubDestination::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id', 'id');
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function assigned_to_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function lead_source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id', 'id');
    }

    public function requirements(): BelongsToMany
    {
        return $this->belongsToMany(Requirement::class, EnquiryRequirement::class);
    }
}
