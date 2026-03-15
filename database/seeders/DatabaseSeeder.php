<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin User',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $teacher = User::firstOrCreate(['email' => 'teacher@example.com'], [
            'name' => 'Teacher User',
            'role' => 'teacher',
            'password' => bcrypt('password123'),
        ]);

        $student = User::firstOrCreate(['email' => 'student@example.com'], [
            'name' => 'Student User',
            'role' => 'student',
            'password' => bcrypt('password123'),
        ]);

        $course = Course::firstOrCreate(['title' => 'Laravel APIs'], [
            'description' => 'Learn how to build APIs in Laravel.',
        ]);

        Enrollment::firstOrCreate([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }
}
