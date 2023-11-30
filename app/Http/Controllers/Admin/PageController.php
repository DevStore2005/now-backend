<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Utils\MyAppEnv;
use App\Utils\PageType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageStoreRequest;
use App\Http\Requests\PageUpdateRequest;

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
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|Factory|Application
    {
        if ($request->query('locale') == 'all' || $request->query('locale') === null) {
            $pages = $this->_page->query()->latest()->get();
        } else {
            $pages = $this->_page
                ->query()
                ->where('country_id', $request->default_country->id)
                ->latest()
                ->get();
        }
        return view('admin.pages.index', [
            'pages' => $pages,
        ]);
    }


    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function create(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|Factory|Application
    {
        $pages = $this->_page->whereIn('type', [PageType::Terms, PageType::Privacy])->get();
        $foundTerms = null;
        $foundPrivacy = null;
        if ($pages) {
            foreach ($pages as $page) {
                if ($page->type == PageType::Terms) {
                    $foundTerms = $page;
                } elseif ($page->type == PageType::Privacy) {
                    $foundPrivacy = $page;
                }
            }
        }
        return view('admin.pages.create', [
            'foundTerms' => $foundTerms,
            'foundPrivacy' => $foundPrivacy,
        ]);
    }


    /**
     * @param PageStoreRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(PageStoreRequest $request): JsonResponse
    {
        try {
            if ($request->hasFile('image')) {
                $common = new Common;;
                $url = $common->store_media($request->image, 'pages');
            }

            $page = $this->_page->create([
                'name' => $request->name,
                'title' => $request->title,
                'content' => $request['content'],
                'type' => $request->type ? $request->type : 0,
                'og_title' => $request->og_title ?? null,
                'og_description' => $request->og_description ?? null,
                'image' => $url ?? null,
                'country_id' => isset($request['default_country']) ? $request['default_country']['id'] : null,
            ]);

            if (isset($request['og_image']) && $request->file('og_image')) {
                $page->addMedia($request['og_image'])->toMediaCollection('og_image');
            }
            if (!$page) {
                return response()->json([
                    'error' => true,
                    'message' => 'Page not created',
                ], HttpStatusCode::CONFLICT);
            } else {
                return response()->json(['status' => HttpStatusCode::OK,
                    'message' => 'Page created successfully',
                    'data' => $page,
                ], HttpStatusCode::OK);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
            // return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Page $page
     * @return Response|null
     */
    public function show(Page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Page $page
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function edit(Page $page): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|Factory|Application
    {
        return view('admin.pages.edit', [
            'page' => $page,
        ]);

    }


    /**
     * @param PageUpdateRequest $request
     * @param Page $page
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(PageUpdateRequest $request, Page $page): JsonResponse
    {
        try {
            if ($request->hasFile('image')) {
                $common = new Common;
                if ($page->image) {
                    $common->delete_media($page->image);
                }
                $url = $common->store_media($request->image, 'pages');
            }

            $page->update([
                'name' => $request->name,
                'title' => $request->title,
                'content' => $request['content'],
                'image' => $url ?? $page->image,
                'og_title' => $request->og_title ?? $page->og_title,
                'og_description' => $request->og_description ?? $page->og_description,
            ]);

            if (isset($request['og_image']) && $request->file('og_image')) {
                $page->clearMediaCollection('og_image');
                $page->addMedia($request['og_image'])->toMediaCollection('og_image');
            }

            return response()->json([
                'status' => HttpStatusCode::OK,
                'message' => 'Page updated successfully',
                'data' => $page,
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param Page $page
     * @return RedirectResponse
     */
    public function destroy(Page $page): RedirectResponse
    {
        $common = new Common;
        if ($page->image) {
            $common->delete_media($page->image);
        }
        $page->delete();
        return redirect()->back()->with('success_message', 'Page deleted successfully');
    }
}
