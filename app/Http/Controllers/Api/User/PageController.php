<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Resources\PagesResource;
use App\Models\Page;
use App\Utils\MyAppEnv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageStoreRequest;
use App\Http\Requests\PageUpdateRequest;
use App\Utils\HttpStatusCode;

class PageController extends Controller
{

    /**
     * @var Page $_page
     * @var string $_environment
     *
     */
    private $_page, $_environment;


    /**
     * Create a new controller instance.
     * @param Page $page
     * @param App $app
     * @return void
     */
    public function __construct(Page $page, App $app)
    {
        $this->_page = $page;
        $this->_environment = $app::environment();
    }


    /**
     * @return PagesResource|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $pages = $this->_page
                ->when($request->query('country_id'), function ($q) use ($request) {
                    return $q->where('country_id', $request->query('country_id'));
                })
                ->get();
            if ($pages->isEmpty()) {
                return response()->json([
                    'message' => 'No pages found',
                    'status' => HttpStatusCode::NOT_FOUND,
                ], HttpStatusCode::NOT_FOUND);
            }
            return new PagesResource($pages);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'Internal server error',
                'status' => HttpStatusCode::INTERNAL_SERVER_ERROR,
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response|null
     */
    public function show(Page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response|null
     */
    public function edit(Page $page)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PageUpdateRequest $request
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response|null
     */
    public function update(PageUpdateRequest $request, Page $page)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Page $page)
    {
        //
    }
}
