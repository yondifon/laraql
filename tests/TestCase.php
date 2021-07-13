<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MakesGraphQLRequests;

    public function auth(User $user = null) : self
    {
        if (! $user) {
            $user = User::factory()->create();
        }

        Sanctum::actingAs($user);

        return $this;
    }
}
