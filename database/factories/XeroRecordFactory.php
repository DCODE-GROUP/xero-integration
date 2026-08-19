<?php

namespace Dcodegroup\XeroIntegration\Database\Factories;

use Dcodegroup\XeroIntegration\Models\XeroRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<XeroRecord>
 */
class XeroRecordFactory extends Factory
{
    protected $model = XeroRecord::class;

    public function definition(): array
    {
        return [
            'recordable_type' => 'App\\Models\\User',
            'recordable_id' => 1,
            'xero_id' => $this->faker->uuid(),
        ];
    }
}
