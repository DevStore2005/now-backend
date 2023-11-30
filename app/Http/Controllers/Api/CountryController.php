<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SliderResource;
use App\Models\Country;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Utils\HttpStatusCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{

    /**
     * @return Builder[]|Collection
     */
    public function index(): Collection|array
    {
        return Country::query()->where('is_active', '=', 1)
            ->select('id', 'name', 'iso2', 'iso3', 'currency', 'currency_symbol', 'emoji', 'is_default', 'stripe_enable')
            ->orderByDesc('is_active')
            ->orderByDesc('is_default')
            ->get();
    }

    /**
     * @param Request $request
     * @return SliderResource|JsonResponse
     */
    public function sliders(Request $request): SliderResource|JsonResponse
    {
        try {
            $sliders = Slider::query()
                ->when($request->query('country_id'), function ($q) use ($request) {
                    return $q->where('country_id', $request->query('country_id'));
                })
                ->get();
            if ($sliders->isEmpty()) {
                return response()->json([
                    'message' => 'No pages found',
                    'data' => [],
                    'error' => true,
                    'status' => HttpStatusCode::NOT_FOUND,
                ], HttpStatusCode::NOT_FOUND);
            }
            return new SliderResource($sliders);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'Internal server error',
                'status' => HttpStatusCode::INTERNAL_SERVER_ERROR,
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

}
