<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\PortfolioRequest;
use App\Http\Resources\PortfoliosResource;
use App\Utils\HttpStatusCode;
use App\Utils\MyAppEnv;

class PortfolioController extends Controller
{
    /**
     * @var \App\Models\User $_user 
     * @var \App\Models\Portfolio $_portfolio
     * @var string $_environment
     */
    private $_user, $_portfolio, $_environment;


    /**
     * Create a new controller instance.
     * @param  \App\Models\User $user
     * @param  \App\Models\Portfolio $portfolio
     * @param  App $app
     * @return void
     */
    public function __construct(User $user, Portfolio $portfolio, App $app)
    {
        $this->_user = $user;
        $this->_portfolio = $portfolio;
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
            Log::error('User:portfolioController -> index', [
                "message: " . $e->getMessage(),
                "Line" . $e->getLine(),
                "File" . $e->getFile()
            ]);
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
     * @return \Illuminate\Http\Response|null
     */
    public function store(PortfolioRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Http\Response|null
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
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Portfolio $portfolio)
    {
        //
    }
}
