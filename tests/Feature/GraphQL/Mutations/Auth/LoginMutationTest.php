<?php

namespace Tests\Feature\GraphQL\Mutations\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginMutationTest extends TestCase
{
    /** @test */
    public function can_user_login_with_right_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secrete'),
        ]);

        $response = $this->graphQL('
            mutation Login ($loginInput: UserLoginInput!) {
                login (input: $loginInput) {
                    token
                    viewer {
                        name
                        email
                    }
                }
            }
        ', [
            'loginInput' => [
                'email'     => $user->email,
                'password'  => 'secrete',
                'tokenName' => 'TestEnv',
            ],
        ]);

        $response->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.login',
                fn ($json) => $json->whereType('token', 'string')
                                    ->where('viewer.name', $user->name)
                                    ->where('viewer.email', $user->email)
            )
        );
    }

    /** @test */
    public function can_not_login_with_wrong_credentials_()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->graphQL('
            mutation Login ($loginInput: UserLoginInput!) {
                login (input: $loginInput) {
                    token
                    viewer {
                        name
                        email
                    }
                }
            }
        ', [
            'loginInput' => [
                'email'     => $user->email,
                'password'  => 'secrete',
                'tokenName' => 'TestEnv',
            ],
        ]);

        $response->assertGraphQLValidationError('password', 'The password or email is incorrect.');
    }

    /** @test */
    public function can_not_login_with_invalid_credentials()
    {
        $response = $this->graphQL('
            mutation Login ($loginInput: UserLoginInput!) {
                login (input: $loginInput) {
                    token
                    viewer {
                        name
                        email
                    }
                }
            }
        ', [
            'loginInput' => [
                'email'     => null,
                'password'  => 'sec',
                'tokenName' => 'TestEnv',
            ],
        ]);

        $response->assertJsonMissingValidationErrors(
            ['input.email', 'input.password'],
            [
                'The input.name field is required.',
                'The input.password must be at least 6 characters.',
            ]
        );
    }
}
