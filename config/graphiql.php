<?php

return [

    'enabled_in_production' => env('GRAPHIQL_IN_PROD', true),

    'route' => [
        'uri' => 'graphiql',

        'prefix' => null,

        'domain' => null,

        'middleware' => [],

        'name' => 'graphiql',
    ],

    'graphql_route'=> env('GRAPHIQL_ROUTE_NAME', 'graphql'),
];
