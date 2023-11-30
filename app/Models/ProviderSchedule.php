<?php

namespace App\Models;

use App\Models\User;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderSchedule extends Model
{

    protected $fillable = ['provider_id', 'year', 'month', 'date', 'full_date'];

    /**
     * Relationship with provider
     *
     */
    public function provider()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with TimeSlots
     *
     * @return HasMany
     */
    public function time_slots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    /**
     * Relationship with Service
     *
     * @return HasOne
     *
     */
    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service');
    }

    /**
     * Relationship with Sub Service
     *
     * @return HasOne
     */
    public function sub_service()
    {
        return $this->hasOne(SubService::class, 'id', 'sub_service_id');
    }

    /**
     * create schedule for provider
     *
     * @param array $data
     * @return void
     */
    public function createSchedule($data)
    {
        $providerSchedule['provider_id'] = auth()->user()->id;
        $providerSchedule['year'] = $data['year'];
        $providerSchedule['month'] = $data['month'];
        foreach ($data['dates'] as $value) {
            $providerSchedule['date'] = $value['date'];
            $schedule = ProviderSchedule::where('provider_id','=',auth()->user()->id)
                ->where('year','=',$providerSchedule['year'])
                ->where('month','=',$providerSchedule['month'])
                ->where('date','=',$providerSchedule['date'])->first();
            if($schedule){
                foreach ($value['slots'] as $slot) {
                    $timeSlot = TimeSlot::where('provider_schedule_id', '=', $schedule->id)
                    ->where('start', '=' , $slot['start'])
                    ->where('end', '=' , $slot['end'])
                    ->first();
                    if(!$timeSlot){
                        $slot['provider_schedule_id'] = $schedule->id;
                        TimeSlot::create($slot);
                    }
                }
            }else {
                $res = ProviderSchedule::create($providerSchedule+['full_date' => $providerSchedule['year'].'-'.$providerSchedule['month'].'-'.$providerSchedule['date']]);
                foreach ($value['slots'] as $slot) {
                    $slot['provider_schedule_id'] = $res->id;
                    TimeSlot::create($slot);
                }
            }
        }
    }
}
