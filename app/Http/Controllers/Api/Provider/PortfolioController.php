<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\User;
use App\Models\Media;
use App\Utils\MyAppEnv;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\PortfolioRequest;
use App\Http\Resources\PortfolioResource;
use App\Http\Resources\PortfoliosResource;

class PortfolioController extends Controller
{
    /**
     * @var \App\Models\User $_user
     * @var \App\Models\Portfolio $_portfolio
     * @var \App\Models\Media $_media
     * @var string $_environment
     */
    private $_user, $_portfolio, $_media, $_environment;


    /**
     * Create a new controller instance.
     * @param  \App\Models\User $user
     * @param  \App\Models\Portfolio $portfolio
     * @param  \App\Models\Media $media
     * @param  App $app
     * @return void
     */
    public function __construct(User $user, Portfolio $portfolio, Media $media, App $app)
    {
        $this->_user = $user;
        $this->_portfolio = $portfolio;
        $this->_media = $media;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|PortfoliosResource
     */
    public function index()
    {
        try {
            $portfolios = $this->_portfolio->where('provider_id', auth()->user()->id)->get();
            if ($portfolios == null) {
                return response()->json([
                    'error' => true,
                    "message" => "No Portfolio Found",
                ]);
            }
            return new PortfoliosResource($portfolios);
        } catch (\Exception $e) {
            Log::error('provider:portfolioController -> store', [$e->getMessage(), $e->getLine(), $e->getFile()]);
            return response()->json([
                'error' => true,
                "message" => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PortfolioRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PortfolioRequest $request)
    {
        try {
            $portfolio = $this->_portfolio->createPortfolio($request->all());
            if (!$portfolio) {
                return response()->json([
                    'error' => true,
                    "message" => "Portfolio not created",
                ], HttpStatusCode::CONFLICT);
            }
            return $portfolio;
        } catch (\Exception $e) {
            Log::error('provider:portfolioController -> store', [$e->getMessage(), $e->getLine(), $e->getFile()]);
            return response()->json([
                'error' => true,
                "message" => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function show(Portfolio $portfolio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Http\JsonResponse|PortfolioResource
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        try {
            $portfolio = $this->_portfolio->updatePortfolio($request->all(['image', 'description']), $portfolio);
            return new PortfolioResource($portfolio);
        } catch (\Exception $e) {
            Log::error('provider:portfolioController -> update', [$e->getMessage(), $e->getLine(), $e->getFile()]);
            return response()->json([
                'error' => true,
                "message" => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Portfolio $portfolio)
    {
        $request->validate([
            'portfolio' => 'array',
            'portfolio.*' => 'required|exists:portfolios,id',
        ]);
        try {
            if (isset($request->portfolio)) {
                $result = $portfolio->where('provider_id', auth()->user()->id)->whereIn('id', $request->portfolio)->delete();
                if ($result) {
                    return response()->json([
                        'error' => false,
                        "message" => "Portfolio deleted successfully",
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        "message" => "Portfolio not found",
                    ], HttpStatusCode::NOT_FOUND);
                }
            };
            if ($portfolio->id) {
                if ($portfolio->provider_id != auth()->user()->id) {
                    return response()->json([
                        'error' => true,
                        "message" => "You are not authorized to delete this portfolio",
                    ], HttpStatusCode::FORBIDDEN);
                }
                $result = $portfolio->delete();
                if ($result) {
                    return response()->json([
                        'error' => false,
                        "message" => "Portfolio deleted successfully",
                    ], HttpStatusCode::OK);
                } else {
                    return response()->json([
                        'error' => true,
                        "message" => "Portfolio not found",
                    ], HttpStatusCode::NOT_FOUND);
                }
            }
            return response()->json([
                'error' => true,
                "message" => "Portfolio not found",
            ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('provider:portfolioController -> destroy', [$e->getMessage(), $e->getLine(), $e->getFile()]);
            return response()->json([
                'error' => true,
                "message" => $this->_environment == MyAppEnv::LOCAL ? "message: " . $e->getMessage() . "Line:  " . $e->getLine() . "File: " . $e->getFile() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete image from portfolio
     * @param  Request $request
     */
    // public function deleteImage(Request $request)
    // {
    //     $request->validate([
    //         'images' => 'required|array',
    //     ]);
    //     try {
    //         $portfolio = $this->_portfolio->where('provider_id', auth()->user()->id)->with('images')->first();
    //         if ($portfolio) {
    //             $images = $portfolio->images;
    //             if ($images) {
    //                 foreach ($request->images as $image) {
    //                     foreach ($images as $value) {
    //                         if ($value->id == $image) {
    //                             $this->_media->where('id', $value->id)->delete();
    //                         }
    //                     }
    //                 }
    //                 return response()->json([
    //                     'error' => false,
    //                     "message" => "Images deleted successfully",
    //                 ], HttpStatusCode::OK);
    //             } else {
    //                 return response()->json([
    //                     'error' => true,
    //                     "message" => "Images not found",
    //                 ], HttpStatusCode::NOT_FOUND);
    //             }
    //         } else {
    //             return response()->json([
    //                 'error' => true,
    //                 "message" => "Portfolio not found",
    //             ], HttpStatusCode::NOT_FOUND);
    //         }
    //     } catch (\Exception $e) {
    //         Log::error(['provider:portfolioController -> deleteImage', $e->getMessage(), $e->getLine(), $e->getFile()]);
    //         return response()->json([
    //             'error' => true,
    //             "message" => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() . $e->getLine() . $e->getFile() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
    //         ], HttpStatusCode::INTERNAL_SERVER_ERROR);
    //     }
    // }
}
