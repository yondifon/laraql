<?php

namespace App\GraphQL\Mutations\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterMutation
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = \collect($args)->only(['name', 'email', 'password'])->toArray();

        $user = $this->createUser($data);

        Auth::login($user);

        $token = $user->createToken($args['tokenName']);

        return [
            'token'  => $token->plainTextToken,
            'viewer' => $user,
        ];
    }

    protected function createUser($data)
    {
        return User::create($data);
    }
}
