<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class RoomMealPlanEntry extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];



    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function meal_plan()
    {
        return $this->belongsTo(MealPlan::class, 'meal_plan_id', 'id');
    }
}
