<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Models\FaqAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use App\Models\SubService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\FaqStoreRequest;
use App\Http\Requests\FaqUpdateRequest;

class FaqController extends Controller
{
    /**
     * Private variable to store the model
     *
     * @var Faq $_faq
     * @var FaqAnswer $_faqAnswer
     * @var SubService $_sub_service
     * @access private
     */
    private $_faq, $_faqAnswer, $_sub_service;

    /**
     * Create a new controller instance.
     * @param Faq $faq
     * @param FaqAnswer $faqAnswer
     * @param SubService $subService
     * @return void
     */
    public function __construct(Faq $faq, FaqAnswer $faqAnswer, SubService $subService)
    {
        $this->_faq = $faq;
        $this->_faqAnswer = $faqAnswer;
        $this->_sub_service = $subService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(Request $request): View
    {
        if ($request->query('locale') == 'all' || $request->query('locale') === null) {
            $faqs = $this->_faq->query()->latest()->get();
        } else {
            $faqs = $this->_faq
                ->query()
                ->where('country_id', $request->default_country->id)
                ->latest()
                ->get();
        }
        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $subServices = $this->_sub_service->get();
        return view('admin.faqs.create', compact('subServices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FaqStoreRequest $request
     * @return RedirectResponse
     */
    public function store(FaqStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data = collect($data)->merge([
            'country_id' => isset($request['default_country']) ? $request['default_country']['id'] : null,
        ])->toArray();
        try {
            $faq = $this->_faq->createFaq($data);
            if ($faq)
                return redirect()
                    ->route('admin.faq.show', ['faq' => $faq->id, 'locale' => $request->query('locale')])
                    ->with('success_message', 'Faq created successfully');
            else
                return redirect()
                    ->back()
                    ->with('error_message', 'Faq not created');
        } catch (\Exception $e) {
            Log::error('context -> FaqController@store', [
                'message' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->with('error_message', "something went wrong");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Faq $faq
     * @return View
     */
    public function show(Faq $faq): View
    {
        $subServices = $this->_sub_service->get();
        return view('admin.faqs.show', compact('faq', 'subServices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Faq $faq
     * @return View
     */
    public function edit(Faq $faq): View
    {
        $subServices = $this->_sub_service->get();
        return view('admin.faqs.edit', compact('faq', 'subServices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FaqUpdateRequest $request
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\Response|null
     */
    public function update(FaqUpdateRequest $request, Faq $faq): RedirectResponse
    {
        try {
            $faq = $faq->updateFaq($request->validated(), $faq);
            if ($faq)
                return redirect()
                    ->back()
                    ->with('success_message', 'Faq updated successfully');
            else
                return redirect()
                    ->back()
                    ->with('error_message', 'Faq not updated');
        } catch (\Exception $e) {
            Log::error('context -> FaqController@update', [
                'message' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->with('error_message', "something went wrong");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        try {
            if ($faq->delete()) {
                return redirect()
                    ->back()
                    ->with('success_message', 'Faq deleted successfully');
            }
            return redirect()
                ->route("admin.faq.index")
                ->with('error_message', 'Something went wrong');
        } catch (\Exception $e) {
            return redirect()
                ->route("admin.faq.index")
                ->with('error_message', "something went wrong");
        }
    }
}
