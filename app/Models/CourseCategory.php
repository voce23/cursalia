<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CourseCategory extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'parent_id', 'status'];

    public function subcategories(): HasMany
    {
        return $this->hasMany(CourseCategory::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'parent_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /**
     * Cursos en todas las subcategorías de esta categoría padre.
     */
    public function allCourses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Course::class,
            CourseCategory::class,
            'parent_id',
            'category_id',
        );
    }
}
