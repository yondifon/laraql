<?php

namespace Tests\Feature\GraphQL\Queries\Auth;

use App\Models\User;
use Tests\TestCase;

class ViewerQueryTest extends TestCase
{
    /** @test */
    public function viewer_can_view_their_info()
    {
        $user = User::factory()->create(
           ['name' => 'Test User']
        );

        $response = $this->auth($user)->graphQL('
            query Viewer {
                viewer {
                    name
                    email
                }
            }
        ');

        $response->assertJsonFragment([
            'viewer' => [
                'name'  => 'Test User',
                'email' => $user->email,
            ],
        ]);
    }
}
