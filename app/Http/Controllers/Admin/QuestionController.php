<?php

namespace App\Http\Controllers\Admin;

use App\Models\Question;
use Illuminate\View\View;
use App\Models\SubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class QuestionController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var Question $_question
     * @var SubService $_subService
     * @access private
     */
    private $_question, $_subService;

    /**
     * Create a new controller instance.
     * @param  \App\Models\Question  $question
     * @param  \App\Models\SubService  $subService
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
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function index($id)
    {
        try {
            $questions = $this->_question->where('sub_service_id', $id)->with('options')->latest()->get();
            $subService = $this->_subService->find($id);
            return view('admin.questions.question_list', compact('questions', 'subService'));
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', 'Something went wrong!');
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
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:1|max:255',
            'options' => 'required|array',
            'options.*' => 'required|string|max:255',
            'sub_service_id' => 'required',
        ]);
        try {
            $this->_question->createQuestion($request->all(['question', 'sub_service_id', 'options', 'is_multiple']));
            return redirect()->back()->with('success_message', 'Question Added');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', 'Something went wrong!');
        }
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
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question' => 'required|string|min:1|max:255',
            'options' => 'required|array',
            'options.*' => 'required|string|max:255',
            'sub_service_id' => 'required',
        ]);

        try {
            $this->_question->updateQuestion($request->all(['question', 'sub_service_id', 'options', 'is_multiple']), $question);
            return redirect()->back()->with('success_message', 'Question Updated');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', 'Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Question $question)
    {
        if ($question->id) {
            $question->options()->delete();
            $question->delete();
            return redirect()->back()->with('success_message', 'Question Deleted');
        }
        return redirect()->back()->with('error_message', 'Something went wrong!');
    }
}
