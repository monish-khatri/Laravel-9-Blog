<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $users = User::get()->pluck('id')->toArray();
        $random_user = array_rand($users);
        return [
            'title' => fake()->text(10),
            'description' => fake()->paragraph(5),
            'user_id' => $users[$random_user],
            'slug' => Str::slug(fake()->text(50) , "-"),
            'status' => 'approved',
            'is_published' => '1',
        ];
    }
}
