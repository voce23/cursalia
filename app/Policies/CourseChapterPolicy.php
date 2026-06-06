<?php

namespace App\Policies;

use App\Models\CourseChapter;
use App\Models\User;

class CourseChapterPolicy
{
    public function update(User $user, CourseChapter $chapter): bool
    {
        return $chapter->instructor_id === $user->id;
    }

    public function delete(User $user, CourseChapter $chapter): bool
    {
        return $chapter->instructor_id === $user->id;
    }
}
