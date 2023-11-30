<?php

namespace App\Models;

use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockedSlot extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'from_time',
        'to_time',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * scope to get blocked slots by date after today
     *
     * @param $query
     */
    public function scopeAfter_or_equal_today($query)
    {
        return $query->where('date', '>=', date('Y-m-d'));
    }

    /**
     * Create or update blocked slot
     *
     * @param array<string> $data
     * @param User $provider
     * @return JsonResponse
     */

    public function createBlockedSlots(array $data, $provider): JsonResponse
    {
        $blockedSlot = $provider->blockedSlots()->updateOrCreate($data);

        if (!$blockedSlot) {
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

        if ($blockedSlot->wasRecentlyCreated) {
            return response()->json([
                'error' => false,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED],
                'data' => $blockedSlot
            ], HttpStatusCode::CREATED);
        }

        return response()->json([
            'error' => true,
            'message' => "Blocked date or slot already exists",
            'data' => $blockedSlot
        ], HttpStatusCode::BAD_REQUEST);
    }

    /**
     * Update blocked slot
     *
     * @param array<string> $data
     * @param BlockedSlot $blockedSlot
     * @return $this
     */
    public function updateBlockedSlots(array $data, BlockedSlot $blockedSlot): BlockedSlot
    {
        $blockedSlot->update($data);
        return $blockedSlot;
    }
}
