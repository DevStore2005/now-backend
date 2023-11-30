<?php

namespace App\Models;

use App\RequestInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    protected $fillable = ['option', 'question_id'];

    /**
     * Relationship With Question
     *
     * @return BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Relationship With RequestInfo
     *
     * @return HasMany
     */
    public function request_infos()
    {
        return $this->hasMany(RequestInfo::class);
    }

    /**
     * Create new option or question
     *
     * @param array $formData
     * @return void
     */
    public function createOption($formData)
    {
        return Option::create($formData);
    }
}
