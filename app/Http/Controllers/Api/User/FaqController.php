<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Faq;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\FaqsResource;
use App\Http\Resources\FaqsResources;

class FaqController extends Controller
{
    /**
     * @var Faq $_faq
     * @var string $_environment
     */
    private $_faq, $_environment;


    /**
     * @param Faq $faq
     * @param string $_environment
     * @param App $app
     */
    public function __construct(Faq $faq, App $app)
    {
        $this->_faq = $faq;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     *
     * @return JsonResponse|FaqsResources
     */
    public function index(Request $request)
    {
        try {

            $faqs = $this->_faq->with('answers:id,faq_id,answer')
                ->when($request->query('country_id'), function ($q) use ($request) {
                    return $q->where('country_id', $request->query('country_id'));
                })
                ->get();
            if ($faqs->isNotEmpty())
                return new FaqsResources($faqs);
            return response()->json([
                'error' => true,
                'message' => 'No faqs found'
            ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error while fetching faqs', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Error while fetching faqs'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Faq $faq
     * @return FaqsResource
     */
    public function show(Faq $faq)
    {
        return new FaqsResource($faq);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function update(Request $request, Faq $faq)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function destroy(Faq $faq)
    {
        //
    }
}
