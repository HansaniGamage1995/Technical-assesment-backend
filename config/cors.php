<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],  // Add your API paths
    'allowed_methods' => ['*'], // Allow all HTTP methods
    'allowed_origins' => ['http://localhost:3000'], // Allow requests from your React app
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Important: Allow credentials
];
