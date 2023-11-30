<?php

namespace App\Models;

use App\RequestInfo;
use App\Models\SubService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = ['question', 'sub_service_id', 'is_multiple'];

    /**
     * Relationship With subService
     *
     * @return BelongsTo
     */
    public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }

    /**
     * Relationship With Question
     *
     * @return HasMany
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Relationship With Question
     *
     * @return HasMany
     */
    public function request_infos()
    {
        return $this->hasMany(RequestInfo::class);
    }

    /**
     * Create new question
     *
     * @param array $formData
     * @return Question
     */
    public function createQuestion($formData)
    {
        $formData['is_multiple'] = filter_var($formData['is_multiple'], FILTER_VALIDATE_BOOLEAN);
        $question = Question::create($formData);
        $optionArray = [];
        foreach ($formData['options'] as $value) {
            $optionArray[] = ['option' => $value];
        }
        $question->options()->createMany($optionArray);
        return $question;
    }

    /**
     * Update question
     *
     * @param array $formData
     * @param Question $question
     * @return Question
     */
    public function updateQuestion($formData, $question)
    {
        $formData['is_multiple'] = filter_var($formData['is_multiple'], FILTER_VALIDATE_BOOLEAN);
        $question->update($formData);
        foreach ($formData['options'] as $key => $value) {
            $options = $question->options();
            $option = $options->find($key);
            if ($option) {
                $option->update(['option' => $value]);
            } else {
                // $options->create(['option' => $value]);
            }
        }
        return $question;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($question) {
            if ($question->options()->exists()) $question->options()->delete();
        });
    }
}
