<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Member> */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            // team_id is NOT NULL with a foreign key, so the factory has to
            // supply one; previously `Member::factory()->create()` always
            // failed unless the caller remembered to pass a team.
            'team_id' => Team::factory(),
        ];
    }
}
