<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd", "sqs", "redis"
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 21600,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 60,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => 'your-public-key',
            'secret' => 'your-secret-key',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
            'queue' => 'your-queue-name',
            'region' => 'us-east-1',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 21600,
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
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

     /*
    |--------------------------------------------------------------------------
    | Job limits
    |--------------------------------------------------------------------------
    */   
    'jobLimits' => [
        /*
        |--------------------------------------------------------------------------
        | Queue entry capping
        |--------------------------------------------------------------------------
        |
        | These options place an upper limit on the number of NEW and RUNNING
        | jobs a user may have in the queue. This cap is applied to snp2gene
        | jobs and all users. The intention is to replace this with a more 
        | sophisticated, per user-group tunable, implementation in a coming FUMA release.
        |
        */
        'queue_cap' => 10, //set to null to remove the cap

        'maxJobs' => [
        /**
         * This is an example of job limits, 
         * the names should correspond to the Roles defined
         * 
         *   'GuestRunner' => 10,
         *   'NormalRunner' => 30,
         *   'SuperRunner' => null,
         *   'TestUser' => 2,
        */
        ],

        'timeouts' => [
        /**
         * This is an example of job timeouts, 
         * the names should correspond to the Roles defined
         *   'GuestRunner' => 3600,
         *   'NormalRunner' => 21600,
         *   'SuperRunner' => 21600,
         *   'TestUser' => 90,
        */
        ], 
    ],

];
