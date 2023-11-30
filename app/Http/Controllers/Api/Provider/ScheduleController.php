<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\Schedule;
use App\Models\BlockedSlots;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleStoreRequest;

class ScheduleController extends Controller
{
    /**
     * @var Schedule $_schedule
     * @access private
     */
    private $_schedule;


    /**
     * Create a new controller instance.
     * @param  \App\Models\Schedule $schedule
     * @return void
     */
    public function __construct(Schedule $schedule)
    {
        $this->_schedule = $schedule;
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $provider = $request->user();
            $schedule = $provider->schedules()->get();
            $blockedSlots = $provider->blockedSlots()->get();
            if ($schedule->isNotEmpty())
                return response()->json([
                    'error' => false,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                'data' => ['schedule' => $schedule, 'blockedSlots' => $blockedSlots]
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('ScheduleController -> getSchedule', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ScheduleStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ScheduleStoreRequest $request): JsonResponse
    {
        try {
            $data = $this->_schedule->createSchedule($request->validated(), $request->user());
            if ($data)
                return response()->json([
                    'error' => false,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED],
                    'data' => $data
                ], HttpStatusCode::CREATED);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CONFLICT]
                ], HttpStatusCode::CONFLICT);
        } catch (\Exception $e) {
            Log::error('ScheduleController -> schedule', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  ScheduleStoreRequest  $request
     * @return \Illuminate\Http\Response|null
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
