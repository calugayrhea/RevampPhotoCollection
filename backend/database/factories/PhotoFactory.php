<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Photo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'collection_id' => 1,
            'file_path' => 'photos/photo.jpg', 
        ];
    }
}