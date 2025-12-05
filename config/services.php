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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Service Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk AI services yang digunakan di MoodBrew.
    | Support multiple providers: OpenAI, Groq (Free), Gemini, Kolosal AI
    |
    | Provider options:
    | - openai: https://api.openai.com/v1
    | - groq: https://api.groq.com/openai/v1 (GRATIS & CEPAT!)
    | - gemini: https://generativelanguage.googleapis.com/v1beta
    | - kolosal: https://api.kolosal.ai/v1
    |
    */

    'ai' => [
        // Provider yang digunakan: openai, groq, gemini, kolosal
        'provider' => env('AI_PROVIDER', 'kolosal'),

        // API Key (dari provider yang dipilih)
        'api_key' => env('AI_API_KEY', ''),

        // API Base URL
        'api_url' => env('AI_API_URL', 'https://api.kolosal.ai/v1'),

        // Model yang digunakan
        // OpenAI: gpt-3.5-turbo, gpt-4, gpt-4-turbo
        // Groq: llama-3.1-70b-versatile, mixtral-8x7b-32768
        // Gemini: gemini-pro
        // Kolosal: meta-llama/llama-4-maverick-17b-128e-instruct
        'model' => env('AI_MODEL', 'meta-llama/llama-4-maverick-17b-128e-instruct'),

        // Temperature (0-1, lebih tinggi = lebih kreatif)
        'temperature' => env('AI_TEMPERATURE', 0.7),

        // Max tokens per response
        'max_tokens' => env('AI_MAX_TOKENS', 500),

        // Enable/disable AI features
        'enabled' => env('AI_ENABLED', true),

        // Fallback ke rule-based jika AI gagal
        'fallback_enabled' => env('AI_FALLBACK_ENABLED', true),
    ],

];
