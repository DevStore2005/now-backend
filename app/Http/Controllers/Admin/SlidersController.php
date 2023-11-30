<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Slider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SlidersController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index(Request $request): \Illuminate\Foundation\Application|View|Factory|Application
    {

        if ($request->query('locale') == 'all' || $request->query('locale') === null) {
            $sliders = Slider::query()->latest()->get();
        } else {
            $sliders = Slider::query()
                ->where('country_id', $request->default_country->id)
                ->latest()
                ->get();
        }

        //Show All Slides
        return view('admin.slider.index', compact('sliders'));
    }

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function create(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return view('admin.slider.create');
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'description' => 'nullable',
            'front_image' => 'required',
            'bg_image' => 'nullable|image',
        ]);

        DB::beginTransaction();
        try {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN);
            $slider = Slider::create([
                'description' => $request->input('description'),
                'country_id' => isset($request['default_country']) ? $request['default_country']['id'] : null,
                'status' => $status,
            ]);

            if ($request->hasFile('bg_image')) {
                $common = new Common;;
                $url = $common->store_media($request->bg_image, 'bg_image');
                $slider->bg_image = $url;
                $slider->save();
            }
            if ($request->hasFile('front_image')) {
                $common = new Common;;
                $url = $common->store_media($request->front_image, 'front_image');
                $slider->front_image = $url;
                $slider->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Slider Successfully Created');
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * @param Slider $slider
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function edit(Slider $slider): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return view('admin.slider.edit', compact('slider'));
    }


    /**
     * @param Request $request
     * @param Slider $slider
     * @return RedirectResponse
     */
    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $request->validate([
            'description' => 'nullable',
            'front_image' => 'nullable|image',
            'bg_image' => 'nullable|image',
        ]);

        DB::beginTransaction();
        try {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN);
            $slider->update([
                'description' => $request->input('description'),
                'status' => $status,
            ]);

            if ($request->hasFile('bg_image')) {
                $common = new Common;
                if ($slider->bg_image) {
                    $common->delete_media($slider->bg_image);
                }
                $url = $common->store_media($request->bg_image, 'bg_image');
                $slider->bg_image = $url;
                $slider->save();
            }
            if ($request->hasFile('front_image')) {
                $common = new Common;
                if ($slider->front_image) {
                    $common->delete_media($slider->front_image);
                }
                $url = $common->store_media($request->front_image, 'front_image');
                $slider->front_image = $url;
                $slider->save();
            }


            DB::commit();

            return redirect()->back()->with('success', 'Slider Successfully Updated');
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return redirect()->back()->with('error', $exception->getMessage());
        }
    }


    /**
     * @param Slider $slider
     * @return RedirectResponse
     */
    public function destroy(Slider $slider): \Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();

        try {
            $common = new Common;
            if ($slider->bg_image) {
                $common->delete_media($slider->bg_image);
            }
            if ($slider->front_image) {
                $common->delete_media($slider->front_image);
            }

            $slider->delete();

            DB::commit();

            return redirect()->route('admin.sliders.index', ['locale' => \request()->query('locale')])->with('success', 'Slider Successfully Deleted');
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return redirect()->route('admin.sliders.index')->with('error', $exception->getMessage());
        }
    }

    /**
     * @param Slider $slider
     * @return RedirectResponse
     */
    public function changeStatus(Slider $slider): RedirectResponse
    {
        $slider->update(['status' => !$slider->status]);

        return redirect()->route('admin.sliders.index', ['locale' => \request()->query('locale')])->with('success', 'Publication status successfully changed');
    }
}
