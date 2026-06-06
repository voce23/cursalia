<?php

namespace App\Policies;

use App\Models\CourseChapterLesson;
use App\Models\User;

class CourseChapterLessonPolicy
{
    public function update(User $user, CourseChapterLesson $lesson): bool
    {
        return $lesson->instructor_id === $user->id;
    }

    public function delete(User $user, CourseChapterLesson $lesson): bool
    {
        return $lesson->instructor_id === $user->id;
    }
}
