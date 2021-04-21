<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    // Por exemplo, se o valor de retry_after for definido como 90, o trabalho será liberado
    // de volta na fila se tiver sido processado por 90 segundos sem ser excluído.
    // Normalmente, você deve definir o retry_after valor para o número máximo de segundos que
    // seus trabalhos devem levar para concluir o processamento.
    // php artisan queue:work --queue=importacao --timeout=60

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

//  php artisan queue:work --queue=importacao
        'importacao' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'importacao',
            'retry_after' => 720,
        ],
// php artisan queue:work --queue=avaliaInspecao
        'avaliaInspecao' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'avaliaInspecao',
            'retry_after' => 720,
        ],
// php artisan queue:work --queue=geraInspecao
        'geraInspecao' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'geraInspecao',
            'retry_after' => 720,
        ],

// php artisan queue:work --queue= geraXmlInspecao
        'geraXmlInspecao' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'geraXmlInspecao',
        'retry_after' => 720,
        ],

        'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 200,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => 0,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
