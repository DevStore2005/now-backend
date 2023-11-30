<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Schedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * @access protected
     */
    protected $fillable = [
        'provider_id',
        'day',
        'from_time',
        'to_time',
        'is_custom',
    ];

    /**
     * Relationship with provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @access public
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * create schedule for provider
     * @param array $data
     * @param User $provider
     *
     * @return Collection
     * @access public
     */
    public function createSchedule(array $data, User $provider): Collection
    {
        $schedules = $provider->schedules();
        if ($schedules->exists()) $schedules->delete();
        if ($data['is_custom']) {
            $data = array_map(function ($item) use ($data) {
                $item['is_custom'] = $data['is_custom'];
                return $item;
            }, $data['days']);
        } else {
            $data = array_map(function ($item) use ($data) {
                $item['is_custom'] = $data['is_custom'];
                $item['from_time'] = $data['from_time'];
                $item['to_time'] = $data['to_time'];
                return $item;
            }, $data['days']);
        }
        return $schedules->createMany($data);
    }
}
