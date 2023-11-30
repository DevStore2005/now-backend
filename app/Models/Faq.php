<?php

namespace App\Models;

use App\Models\FaqAnswer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var string[]
     * @access protected
     */
    protected $fillable = ['question', 'sub_service_id', 'country_id'];

    /**
     * Summary of with
     * @var string[]
     * @access protected
     */
    protected $with = ['answers:id,faq_id,answer', 'sub_service:id,name'];

    /**
     * The answers that belong to the Faq
     * @return HasMany
     * @access public
     */
    public function answers(): HasMany
    {
        return $this->hasMany(FaqAnswer::class);
    }

    /**
     * Get the sub service that owns the Faq
     * @return BelongsTo
     * @access public
     */
    public function sub_service(): BelongsTo
    {
        return $this->belongsTo(SubService::class);
    }

    /**
     * Create Faq and FaqAnswer
     * @param array $data
     * @return Faq
     * @access public
     */
    public function createFaq(array $data): Faq
    {
        $faq = $this->create($data);
        $answers = $this->createAnswers($data['answers']);
        $faq->answers()->createMany($answers);
        return $faq;
    }

    /**
     * Update Faq and FaqAnswer
     * @param array $data
     * @param $this $faq
     * @return Faq
     * @access public
     */
    public function updateFaq(array $data, $faq): Faq
    {
        $faq->update($data);
        $faq->answers()->delete();
        $answers = $this->createAnswers($data['answers']);
        $faq->answers()->createMany($answers);
        return $faq;
    }

    /**
     * createAnswers
     * @param array $data
     * @return array<string>
     * @access private
     */
    private function createAnswers(array $data): array
    {
        return array_map(function ($answer) {
            return ['answer' => $answer];
        }, $data);
    }
}
