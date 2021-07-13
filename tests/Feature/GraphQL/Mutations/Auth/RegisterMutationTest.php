<?php

namespace Tests\Feature\GraphQL\Mutations\Auth;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterMutationTest extends TestCase
{
    /** @test */
    public function guest_can_register_new_account()
    {
        $user = User::factory()->make();

        $response = $this->graphQL('
            mutation Register ($registerInput: UserRegistrationInput!) {
                register (input: $registerInput) {
                    token
                    viewer {
                        name
                        email
                    }
                }
            }
        ', [
            'registerInput' => [
                'name'      => $user->name,
                'email'     => $user->email,
                'password'  => 'secrete',
                'tokenName' => 'TestEnv',
            ],
        ]);

        dd($response);
        $this->assertDatabaseHas('users', [
            'name'  => $user->name,
            'email' => $user->email,
        ]);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.register',
                fn ($json) => $json->whereType('token', 'string')
                                    ->where('viewer.name', $user->name)
                                    ->where('viewer.email', $user->email)
            )
        );
    }

    /** @test */
    public function can_not_register_with_invalid_credentials_()
    {
        $response = $this->graphQL('
            mutation Register ($registerInput: UserRegistrationInput!) {
                register (input: $registerInput) {
                    token
                    viewer {
                        name
                        email
                    }
                }
            }
        ', [
            'registerInput' => [
                'name'      => '',
                'email'     => '',
                'password'  => 'set',
                'tokenName' => 'TestEnv',
            ],
        ]);

        $response->assertJsonMissingValidationErrors(
            ['input.name', 'input.email', 'input.password'],
            [
                'The input.name field is required.',
                'The input.name field is required.',
                'The input.password must be at least 6 characters.',
            ]
        );
    }
}
