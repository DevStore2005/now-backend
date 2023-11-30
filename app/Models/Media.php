<?php

namespace App\Models;

use App\Models\User;
use App\Utils\MediaType;
use App\Models\Portfolio;
use Illuminate\Support\Arr;
use App\Http\Helpers\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Node\ElementNode;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type', 'name', 'url', 'quotation_info_id',
    ];


    /*************************************** Public Function ***************************************/
    /**
     * Relationship with Media
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Quotation
     *
     * @return BelongsTo
     */
    public function service_request()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Portfolio
     *
     * @return BelongsTo
     */
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    /**
     * store License and docs
     *
     * @param array $docs
     * @return media|array
     */
    public function storeDocs($docs)
    {
        $imageType = ['png' => 'png', 'jpeg' => 'jpeg', 'jpg' => 'jpg'];
        $fileType = ['pdf' => 'pdf', 'doc' => 'doc', 'docx' => 'docx'];

        foreach ($docs['docs'] as $doc) {

            $ext = $doc->extension();

            try {
                if (Arr::exists($imageType, $ext)) {
                    $path = '/public/provider/docs/images';
                    $this->_storeDocsInStorage($path, $doc, $ext, MediaType::IMAGE);
                } elseif (Arr::exists($fileType, $ext)) {
                    $path = '/public/provider/docs/files';
                    $this->_storeDocsInStorage($path, $doc, $ext, MediaType::FILE);
                } else {
                    return ['error' => 'Not Allow with .' . $ext];
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return Media::where('user_id', '=', auth()->user()->id);
    }


    /************************************ Private Function **************************************/
    /**
     * save docs in Storage
     *
     * @param string $path
     * @param object $doc
     * @return void
     */
    private function _storeDocsInStorage($path, $doc,  $ext, $type)
    {
        try {
            $name = uniqid() . '-' . time() . "." . $ext;
            if (Storage::exists($path)) {
                $this->_createMedia($name, $doc->storeAs($path, $name), $type);
            } else {
                Storage::makeDirectory($path);
                $this->_createMedia($name, $doc->storeAs($path, $name), $type);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Create record on media table
     *
     * @param string $name, $path, $type
     * @return void
     */
    private function _createMedia($name, $path, $type)
    {
        try {
            Media::create([
                'user_id' => auth()->user()->id,
                'type' => $type,
                'name' => $name,
                'url' => Storage::url($path)
            ]);
        } catch (\Throwable $th) {
            Storage::delete($path);
            throw $th;
        }
    }

    /**
     * boot function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        $common = new Common();
        self::deleting(function ($model) use ($common) {
            $common->delete_media($model->url);
        });
    }
}
