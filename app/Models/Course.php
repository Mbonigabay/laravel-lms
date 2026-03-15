<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ka4ivan\LaravelLogger\Models\Traits\HasTracking;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory, HasTracking;

    protected $fillable = ['title', 'description'];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
