<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExtraInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function index()
    {
        $partners = ExtraInfo::where('type', 'Partner')->get();
        return view('admin.front_page.partner', compact('partners'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'url',
            'image' => 'required|image',
        ]);
        try {
            $data = [
                'name' => $request->name,
                'url' => $request->url ?? null,
                'type' => 'Partner',
            ];

            if ($request->hasFile('image')) {
                $common = new Common();
                $url = $common->store_media($request->image, 'partner');
                $data['image'] = $url;
            }

            if (ExtraInfo::create($data)) {
                return redirect()->back()->with('success_message', 'Partner created successfully');
            }
            return redirect()->back()->with('error_message', 'Something went wrong');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function update(Request $request, ExtraInfo $partner): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'url' => 'url',
            'image' => 'nullable|image',
        ]);
        try {
            $data = [
                'name' => $request->name,
                'url' => $request->url ?? null,
                'type' => 'Partner',
            ];

            if ($request->hasFile('image')) {
                $common = new Common();
                if ($partner->image) {
                    $common->delete_media($partner->image);
                }
                $url = $common->store_media($request->image, 'partner');
                $data['image'] = $url;
            }
            if ($partner->update($data)) {
                return redirect()->back()->with('success_message', 'Partner updated successfully');
            }
            return redirect()->back()->with('error_message', 'Something went wrong');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param ExtraInfo $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExtraInfo $partner)
    {
        try {
            if ($partner->delete()) {
                return redirect()->back()->with('success_message', 'Partner deleted successfully');
            }
            return redirect()->back()->with('error_message', 'Something went wrong');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }
}
