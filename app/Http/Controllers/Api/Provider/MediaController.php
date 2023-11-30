<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Utils\AppConst;

class MediaController extends Controller
{
    /**
     * @var Media $_media
     */
    private $_media;

    /**
     * Create a new controller instance.
     * @param  \App\Models\Media $media
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->_media = $media;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $docs = $this->_media->where('user_id',auth()->user()->id)
            ->where('quotation_info_id', null)
            ->paginate(AppConst::PAGE_SIZE);

            if(!$docs->isEmpty()){
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $docs
                ], HttpStatusCode::OK);
            }
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('MediaController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'docs.*' => 'required|mimes:png,jpeg,jpg,pdf,doc,docx|max:5120',
        ]);
        try {
            $data = $this->_media->storeDocs($request->all());
            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => $data
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('MediaController -> store', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => 'something went wrong!'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response|null
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response|null
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Media $media
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media $media
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Media $media)
    {
        //
    }
}
