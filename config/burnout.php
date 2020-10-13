<?php /** @noinspection PhpFullyQualifiedNameUsageInspection */

return [
    'enabled' => env('BURNOUT_ENABLED', false),

    'allowed_users' => env('BURNOUT_ALLOWED_EMAILS', ''),

    'delete_logs_older_than_days' => env('BURNOUT_LOGS_VALIDITY_DAYS', 15),

    'skip_exceptions' => [
        \Illuminate\Validation\ValidationException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
    ]
];
