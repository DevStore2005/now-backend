<?php

namespace App\Models;

use App\Models\User;
use App\Models\Media;
use App\Utils\MediaType;
use App\Http\Helpers\Common;
use GuzzleHttp\Promise\Each;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'image',
        'description',
        'status',
    ];

    /**
     * Relationships with Media model.
     */
    public function images()
    {
        return $this->hasMany(Media::class, 'portfolio_id');
    }

    /**
     * Relationships with user (Provider).
     */
    public function provider()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the portfolio scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved_portfolios($query)
    {
        return $query->where('status', true);
    }

    /**
     * Create new portfolio
     * 
     * @param  array $data
     */
    public function createPortfolio(array $data)
    {

        try {
            $array = [];
            $common = new Common();
            for ($index = 0; $index <= $data['length']; $index++) {
                if (isset($data['images_' . $index]) && isset($data['description_' . $index])) {
                    $path = 'provider/portfolio';
                    $url = $common->store_media($data['images_' . $index], $path);
                    $created = Portfolio::create([
                        'provider_id' => auth()->user()->id,
                        'image' => $url,
                        'description' => $data['description_' . $index],
                    ]);
                    $array[] = $created;
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return $array;
        // $this->description = $data['description'];
        // $this->save();
        // foreach ($data['images'] as $image) {
        //     try {
        //         $this->images()->create([
        //             'portfolio_id' => $this->id,
        //             'url' => $url,
        //             'type' => MediaType::IMAGE,
        //         ]);
        //     } catch (\Throwable $th) {
        //         $this->destroy($this->id);
        //         throw $th;
        //     }
        // }
        // return $this;
    }

    /**
     * Update portfolio
     * 
     * @param  array $data
     * @param  Portfolio $portfolio
     */
    public function updatePortfolio(array $data, Portfolio $portfolio)
    {
        if (isset($data['description'])) {
            $portfolio->description = $data['description'];
            $portfolio->save();
        }
        if (isset($data['image']) && $data['image'] != null) {
            $common = new Common();
            $url = $common->delete_media($portfolio->image);
            $path = 'provider/portfolio';
            try {
                $url = $common->store_media($data['image'], $path);
                $portfolio->image = $url;
                $portfolio->save();
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return $portfolio;
    }

    /**
     *  Change status of portfolio
     *
     * @param string $type
     * @param int $id
     * @param bool $status
     */
    public function changeStatus($type, $id, $status)
    {
        if ($type == 'all') {
            return $this->whereProvider_id($id)->update(['status' => $status]);
        } else {
            return $this->find($id)->update(['status' => boolval($status)]);
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
            $common->delete_media($model->image);
        });
    }
}
