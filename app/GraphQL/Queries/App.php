<?php

namespace App\GraphQL\Queries;

use Illuminate\Foundation\Application;

class App
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            'name'           => config('app.name'),
            'environment'    => config('app.env'),
            'laravelVersion' => Application::VERSION,
            'phpVersion'     => \PHP_VERSION,
        ];
    }
}
