<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ka4ivan\LaravelLogger\Models\Traits\HasTracking;

class Quiz extends Model
{
    use HasTracking;

    protected $fillable = ['course_id', 'title'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }
}
