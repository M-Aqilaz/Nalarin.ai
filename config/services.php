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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
        'allow_gif_avatars' => (bool) env('DISCORD_AVATAR_GIF', true),
        'avatar_default_extension' => env('DISCORD_EXTENSION_DEFAULT', 'png'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model' => env('OPENAI_MODEL', 'openai/gpt-oss-120b:free'),
        'timeout' => env('OPENAI_TIMEOUT', 60),
        'max_output_tokens' => env('OPENAI_MAX_OUTPUT_TOKENS', 800),
        'content_max_output_tokens' => env('OPENAI_CONTENT_MAX_OUTPUT_TOKENS', 1800),
        'limits' => [
            'free_per_minute' => env('AI_CHAT_FREE_PER_MINUTE', 4),
            'free_per_day' => env('AI_CHAT_FREE_PER_DAY', 10),
            'premium_per_minute' => env('AI_CHAT_PREMIUM_PER_MINUTE', 20),
            'content_free_per_minute' => env('AI_CONTENT_FREE_PER_MINUTE', 2),
            'content_free_per_day' => env('AI_CONTENT_FREE_PER_DAY', 6),
            'content_premium_per_minute' => env('AI_CONTENT_PREMIUM_PER_MINUTE', 10),
        ],
    ],

    'ocr' => [
        'enabled' => env('OCR_ENABLED', true),
        'languages' => env('OCR_LANGUAGES', 'ind+eng'),
        'timeout' => env('OCR_TIMEOUT', 120),
        'min_text_length' => env('OCR_MIN_TEXT_LENGTH', 120),
        'free_max_pages' => env('OCR_FREE_MAX_PAGES', 5),
        'premium_max_pages' => env('OCR_PREMIUM_MAX_PAGES', 50),
        'tesseract_path' => env('OCR_TESSERACT_PATH', 'tesseract'),
        'pdftotext_path' => env('OCR_PDFTOTEXT_PATH', 'pdftotext'),
        'pdftoppm_path' => env('OCR_PDFTOPPM_PATH', 'pdftoppm'),
    ],

];
