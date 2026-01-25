<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        $fileable = fake()->randomElement([
            ['type' => Project::class, 'id' => Project::factory()],
            ['type' => Task::class, 'id' => Task::factory()],
        ]);

        $extension = fake()->randomElement(['pdf', 'docx', 'xlsx', 'jpg', 'png', 'dwg']);

        return [
            'filename' => fake()->word() . '.' . $extension,
            'original_filename' => fake()->word() . '.' . $extension,
            'file_path' => 'files/' . fake()->uuid() . '.' . $extension,
            'file_size' => fake()->numberBetween(1024, 10485760), // 1KB to 10MB
            'mime_type' => match($extension) {
                'pdf' => 'application/pdf',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'dwg' => 'application/acad',
            },
            'uploaded_by' => User::factory(),
            'document_type_id' => DocumentType::factory(),
            'description' => fake()->optional()->sentence(),
            'fileable_type' => $fileable['type'],
            'fileable_id' => $fileable['id'],
        ];
    }

    public function forProject(): static
    {
        return $this->state(fn (array $attributes) => [
            'fileable_type' => Project::class,
            'fileable_id' => Project::factory(),
        ]);
    }

    public function forTask(): static
    {
        return $this->state(fn (array $attributes) => [
            'fileable_type' => Task::class,
            'fileable_id' => Task::factory(),
        ]);
    }

    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => fake()->word() . '.pdf',
            'original_filename' => fake()->word() . '.pdf',
            'file_path' => 'files/' . fake()->uuid() . '.pdf',
            'mime_type' => 'application/pdf',
        ]);
    }

    public function image(): static
    {
        $extension = fake()->randomElement(['jpg', 'png']);
        return $this->state(fn (array $attributes) => [
            'filename' => fake()->word() . '.' . $extension,
            'original_filename' => fake()->word() . '.' . $extension,
            'file_path' => 'files/' . fake()->uuid() . '.' . $extension,
            'mime_type' => $extension === 'jpg' ? 'image/jpeg' : 'image/png',
        ]);
    }
}
