<?php

namespace App\Models;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqAnswer extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var string[]
     */
    protected $fillable = ['faq_id', 'answer'];

    /**
     * The faq that belong to the FaqAnswer
     * @return BelongsTo
     */
    public function faq(): BelongsTo
    {
        return $this->belongsTo(Faq::class);
    }
}
