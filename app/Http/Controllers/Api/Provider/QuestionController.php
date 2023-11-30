<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\Question;
use App\Models\SubService;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class QuestionController extends Controller
{
    /**
     * @var Question $_question
     * @var SubService $_subService
     */
    private $_question, $_subService;

    /**
     * Create a new controller instance.
     * @param  \App\Models\Question  $question
     * @return void
     */
    public function __construct(Question $question, SubService $subService)
    {
        $this->_question = $question;
        $this->_subService = $subService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
       try {
            $questions = $this->_question->where('sub_service_id', $id)->with('options')->get();
            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => $questions
            ], HttpStatusCode::OK);
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
