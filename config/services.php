<?php

return [
    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'key' => env('SUPABASE_PUBLIC_KEY'),
    ],
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],
];
