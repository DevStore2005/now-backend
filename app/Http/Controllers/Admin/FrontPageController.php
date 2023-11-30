<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Models\Currency;
use App\Models\FrontPage;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FrontPageStoreRequest;

class FrontPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $country = request('country');
        $countries = Country::where('is_active', 1)->get();
        if ($country) {
            $country = Arr::first($countries, function ($value) use ($country) {
                return $value->iso2 == $country;
            });
        }
        $frontPages = FrontPage::when($country && $country->id, function ($qry) use ($country) {
            return $qry->where('country_id', $country->id);
        })->with('app_urls', 'extra_info')->get();
        return view('admin.front_page.index', compact('countries', 'country', 'frontPages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FrontPageStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FrontPageStoreRequest $request)
    {
        try {
            $common = new Common();
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $url = $common->store_media($request->image, 'front_page');
                $data['image'] = $url;
            }
            FrontPage::create($data);
            return redirect()->back()->with('success_message', 'Front page created successfully');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    public function appUrls(Request $request)
    {
        try {
            $request->validate([
                'user_android' => 'required|url',
                'provider_android' => 'required|url',
                'user_ios' => 'required|url',
                'provider_ios' => 'required|url'
            ]);
            $frontPage = FrontPage::whereType('App')->first();
            if (!$frontPage) {
                return redirect()->back()->with('error_message', 'This section is not available');
            }
            if ($frontPage->app_urls()->exists()) {
                $frontPage->app_urls()->delete();
            }
            $frontPage->app_urls()->createMany([
                [
                    'name' => 'user_android',
                    'url' => $request->user_android
                ],
                [
                    'name' => 'provider_android',
                    'url' => $request->provider_android
                ],
                [
                    'name' => 'user_ios',
                    'url' => $request->user_ios
                ],
                [
                    'name' => 'provider_ios',
                    'url' => $request->provider_ios
                ]
            ]);
            return redirect()->back()->with('success_message', 'App urls updated successfully');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    public function appInfo(Request $request)
    {
        $request->validate([
            'title_1' => 'required|min:1|max:255',
            'description_1' => 'required|min:1|max:1000',
            'title_2' => 'required|min:1|max:255',
            'description_2' => 'required|min:1|max:1000',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable'
        ]);
        try {
            $frontPage = FrontPage::whereType('Info')->first();
            if (!$frontPage) {
                return redirect()->back()->with('error_message', 'This section is not available');
            }
            $url = [];
            if ($request->hasFile('image')) {
                $common = new Common();
                $url['image'] = $common->store_media($request->image, 'front_page');
            }
            $extra_info = $frontPage->extra_info()->first();
            if (!$extra_info) {
                $frontPage->extra_info()->create([
                    'name' => "",
                    'title_1' => $request->title_1,
                    'description_1' => $request->description_1,
                    'title_2' => $request->title_2,
                    'description_2' => $request->description_2,
                    'image' => $url['image'] ?? null,
                ]);
                return redirect()->back()->with('success_message', 'App info Add successfully');
            }
            if ($extra_info->image && isset($url['image'])) {
                $common = new Common();
                $common->delete_media($extra_info->image);
                $extra_info->image = $url['image'] ?? $extra_info->image;
            }
            $extra_info->title_1 = $request->title_1;
            $extra_info->description_1 = $request->description_1;
            $extra_info->title_2 = $request->title_2;
            $extra_info->description_2 = $request->description_2;
            $extra_info->save();
            return redirect()->back()->with('success_message', 'App info updated successfully');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FrontPage $frontPage
     * @return \Illuminate\Http\Response|null
     */
    public function show(FrontPage $frontPage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\FrontPage $frontPage
     * @return \Illuminate\Http\Response|null
     */
    public function edit(FrontPage $frontPage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FrontPage $frontPage
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, FrontPage $frontPage)
    {
        try {
            if ($request->hasFile('image')) {
                $common = new Common();
                $url = $common->store_media($request->image, 'front_page');
                if ($frontPage->image) $common->delete_media($frontPage->image);
                $frontPage->image = $url;
            }
            $frontPage->title = $request->title;
            $frontPage->description = $request->description;
            $frontPage->save();
            return redirect()->back()->with('success_message', 'Front page updated successfully');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FrontPage $frontPage
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(FrontPage $frontPage)
    {
        //
    }
}
