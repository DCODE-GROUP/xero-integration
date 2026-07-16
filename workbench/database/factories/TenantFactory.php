<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Tenant;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition()
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
        ];
    }
}
