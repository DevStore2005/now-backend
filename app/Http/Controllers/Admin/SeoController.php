<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeoResource;
use App\Http\Resources\SeosResource;
use App\Models\Blog;
use App\Models\Page;
use App\Models\Seo;
use App\Utils\HttpStatusCode;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SeoController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $seos = Seo::latest()->get();
        return view('admin.seos.index', compact('seos'));
    }

    public function create()
    {
        return view('admin.seos.create');
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'og_title' => 'required',
        ]);
        try {
            $seo = Seo::create([
                'page_name' => $request->input('page_name'),
                'og_title' => $request->input('og_title'),
                'og_description' => $request->input('og_description'),
            ]);
            if ($request['og_image'] && $request->file('og_image')) {
                $seo->addMedia($request['og_image'])->toMediaCollection('og_image');
            }
            return redirect()->back()->with('success_message', 'Successfully Created');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error_message', $exception->getMessage());
        }
    }


    public function edit(Seo $seo)
    {
        return view('admin.seos.edit', compact('seo'));
    }

    /**
     * @param Request $request
     * @param Seo $seo
     * @return RedirectResponse
     */
    public function update(Request $request, Seo $seo): RedirectResponse
    {
        $request->validate([
            'og_title' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $seo->update([
                'page_name' => $request->input('page_name'),
                'og_title' => $request->input('og_title'),
                'og_description' => $request->input('og_description'),
            ]);
            if (isset($request['og_image']) && $request->file('og_image')) {
                $seo->clearMediaCollection('og_image');
                $seo->addMedia($request['og_image'])->toMediaCollection('og_image');
            }
            DB::commit();
            return redirect()->back()->with('success_message', 'Successfully Updated');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error_message', $exception->getMessage());
        }
    }


    /**
     * @param Seo $seo
     * @return RedirectResponse
     */
    public function destroy(Seo $seo): RedirectResponse
    {
        try {
            $seo->clearMediaCollection('og_image');
            $seo->delete();
            return redirect()->back()->with('success_message', 'Successfully Deleted');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error_message', $exception->getMessage());
        }
    }

    /**
     * @return SeosResource
     */
    public function getAll()
    {
        return new SeosResource(Seo::latest()->get());
    }
}
