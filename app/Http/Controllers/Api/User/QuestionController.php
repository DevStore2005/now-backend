<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Question;
use App\Models\SubService;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class QuestionController extends Controller
{

    /**
     * Question Controller variable
     *
     * @var \App\Models\User $_user
     * @var \App\Models\Question $_question
     * @var \App\Models\SubService $_subService
     * @var \App\Models\ServiceRequest $_serviceRequest
     */
    private $_question, $_user, $_serviceRequest, $_subService;

    /**
     * Create a new controller instance.
     * @param  \App\Models\Question  $question
     * @return void
     */
    public function __construct(Question $question, SubService $subService, User $user, ServiceRequest $serviceRequest)
    {
        $this->_question = $question;
        $this->_subService = $subService;
        $this->_user = $user;
        $this->_serviceRequest = $serviceRequest;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        try {
            $serviceRequest = DB::table('service_requests')
                ->select('hours')
                ->inRandomOrder()
                ->where('hours', '!=', null)
                ->where('sub_service_id', '!=', $id)
                ->groupBy('hours')
                ->limit(6)
                ->get();
            $data = $this->_subService->with([
                'questions',
                'questions.options',
                'service_contents'
            ])->find($id);
            $data->service_requests = $serviceRequest;
            $data->provider = $this->_user->provider()
                ->whereHas('provider_services', function ($query) use ($id) {
                    return $query->where('sub_service_id', $id);
                })->withCount(['provider_service_requests' => function ($q) {
                    return $q->whereIs_completed(true);
                }])->oldest()->take(3)->get();


            if ($data !== null && count($data->questions) > 0) {
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $data
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('QuestionController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|null
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response|null
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response|null
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Question $question)
    {
        //
    }
}
