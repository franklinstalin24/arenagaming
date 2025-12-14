<?php

return [
    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase.json')),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    'verify_ssl' => env('FIREBASE_VERIFY_SSL', true),
];
