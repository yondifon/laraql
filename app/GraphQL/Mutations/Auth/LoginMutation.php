<?php

namespace App\GraphQL\Mutations\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Exceptions\ValidationException;

class LoginMutation
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $credentials = \collect($args)->only(['email', 'password'])->toArray();

        if (! Auth::attempt($credentials)) {
            throw new ValidationException('Invalid Credentials', $this->validator($args));
        }

        $user = Auth::user();
        $token = $user->createToken($args['tokenName']);

        return [
            'token'  => $token->plainTextToken,
            'viewer' => $user,
        ];
    }

    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'email'    => 'exists:users',
                'password' => 'current_password',
            ],
            [
                'password.current_password' => 'The password or email is incorrect.',
            ]
        );
    }
}
