<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\BlockedSlot;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlockedSlotStoreRequest;
use App\Http\Requests\BlockedSlotUpdateRequest;

class BlockedSlotController extends Controller
{
    /**
     * @var BlockedSlot $_blockedSlot
     * @access private
     */
    private $_blockedSlot;


    /**
     * Create a new controller instance.
     * @param  BlockedSlot $blockedSlot
     * @return void
     */
    public function __construct(BlockedSlot $blockedSlot)
    {
        $this->_blockedSlot = $blockedSlot;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function index()
    {
        try {
            $data = $this->_blockedSlot->After_or_equal_today()->get();
            if ($data->isNotEmpty())
                return response()->json([
                    'error' => false,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                    'data' => $data
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('BlockedSlotController -> index', [$e->getMessage(), $e->getLine()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BlockedSlotStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BlockedSlotStoreRequest $request)
    {
        try {
            return $this->_blockedSlot->createBlockedSlots($request->validated(), $request->user());
        } catch (\Exception $e) {
            Log::error('BlockedSlotController -> store', [$e->getMessage(), $e->getLine()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BlockedSlotUpdateRequest  $request
     * @param  BlockedSlot  $blockedSlot
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function update(BlockedSlotUpdateRequest $request, BlockedSlot $blockedSlot): JsonResponse
    {
        try {
            $blockedSlot = $this->_blockedSlot->updateBlockedSlots($request->validated(), $blockedSlot);
            if ($blockedSlot)
                return response()->json([
                    'error' => false,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                    'data' => $blockedSlot
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CONFLICT]
                ], HttpStatusCode::CONFLICT);
        } catch (\Exception $e) {
            Log::error('BlockedSlotController -> update', [$e->getMessage(), $e->getLine()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  BlockedSlot $blockedSlot
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function destroy(BlockedSlot $blockedSlot): JsonResponse
    {
        try {
            $blockedSlot = $blockedSlot->delete();
            if ($blockedSlot)
                return response()->json([
                    'error' => false,
                    'message' => "Blocked slot deleted successfully",
                    'data' => $blockedSlot
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CONFLICT]
                ], HttpStatusCode::CONFLICT);
        } catch (\Exception $e) {
            Log::error('BlockedSlotController -> destroy', [$e->getMessage(), $e->getLine()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
