<?php

namespace App\Http\Controllers\Api\Provider;

use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Credit;

class CreditController extends Controller
{
    /**
     * @var Credit $credit
     */
    private $_credit;

    /**
     * Create a new controller instance.
     * @param  \App\Models\Media $media
     * @return void
     */
    public function __construct(Credit $credit)
    {
        $this->_credit = $credit;
    }

    /**
     * show Credit history to Provider.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $creditHitory = $this->_credit->whereProvider_id($request->user()->id)->paginate(20);
            if ($creditHitory)
                return response()->json([
                    'error' => false,
                    'data' => $creditHitory,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('TransactionController -> makeTransaction', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

}
