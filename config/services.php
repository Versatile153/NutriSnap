<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'clarifai' => [
        'url' => env('CLARIFAI_API_URL', 'https://api.clarifai.com/v2/models/aaa03c23b3724a16a56b629203edc62c/outputs'),
        'key' => env('CLARIFAI_API_KEY'),
    ],

    'nutrition_api' => [
        'url' => env('NUTRITION_API_URL', 'https://api.nal.usda.gov/fdc/v1/foods/search'),
        'key' => env('NUTRITION_API_KEY'),
    ],

    'ai_training_endpoint' => env('AI_TRAINING_ENDPOINT', 'https://api.clarifai.com/v2/inputs'),

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'vision_api' => [
        'url' => env('VISION_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent'),
        'key' => env('VISION_API_KEY'),
        'cloud_vision_url' => env('CLOUD_VISION_API_URL', 'https://vision.googleapis.com/v1/images:annotate'),
    ],

    'fireworks' => [
        'key' => env('FIREWORKS_API_KEY'),
        'url' => env('FIREWORKS_API_URL', 'https://api.fireworks.ai/inference/v1/chat/completions'),
    ],

    'google_cloud' => [
        'project_id' => env('GOOGLE_CLOUD_PROJECT'),
        'credentials' => env('GOOGLE_CLOUD_CREDENTIALS'),
    ],

    'edamam' => [
        'app_id' => env('EDAMAM_APP_ID'),
        'app_key' => env('EDAMAM_APP_KEY'),
    ],

    'openai' => [
        'key' => env('OPENAI_KEY'),
        'url' => env('OPENAI_URL', 'https://api.openai.com/v1/chat/completions'),
    ],

    'logmeal' => [
        'key' => env('LOGMEAL_KEY'),
        'url' => env('LOGMEAL_URL', 'https://api.logmeal.es/v2'),
    ],

   'huggingface' => [
    'url' => env('HUGGINGFACE_URL', 'https://versatile153-nutri.hf.space/run/predict'),
    'key' => env('HUGGINGFACE_API_KEY'),

    ],

];