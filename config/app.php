<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', '')),
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Use Resend to send transactional emails
    |--------------------------------------------------------------------------
    |
    | This value enables the use of Resend to send transactional emails.
    | If you self host the application, you probably want to disable this
    | since you don't need to send transactional emails.
    |
    */

    'use_resend' => env('USE_RESEND', false),

    /*
    |--------------------------------------------------------------------------
    | Supported locales
    |--------------------------------------------------------------------------
    |
    | This value enables the supported locales of the application.
    |
    */

    'supported_locales' => ['en', 'fr'],

    /*
    |--------------------------------------------------------------------------
    | Email that receives account deletion notifications
    |--------------------------------------------------------------------------
    |
    | This email is used to receive notifications when an account is deleted.
    |
    */

    'account_deletion_notification_email' => env('ACCOUNT_DELETION_NOTIFICATION_EMAIL', 'hello@example.com'),

    /*
    |--------------------------------------------------------------------------
    | List of reserved keywords that cannot be used as organization names
    |--------------------------------------------------------------------------
    |
    | This list contains keywords that are reserved and cannot be used as
    | organization names. This is to prevent confusion and potential security
    | issues.
    |
    */
    'reserved_organization_keywords' => [
        'admin',
        'app',
        'api',
        'auth',
        'login',
        'logout',
        'register',
        'signup',
        'signin',
        'signout',
        'session',
        'sessions',
        'account',
        'accounts',
        'me',
        'user',
        'users',
        'profile',
        'profiles',
        'settings',
        'preferences',
        'dashboard',
        'home',
        'root',
        'system',
        'internal',
        'platform',
        'central',
        'tenant',
        'tenants',
        'organization',
        'organizations',
        'about',
        'company',
        'team',
        'careers',
        'jobs',
        'pricing',
        'plans',
        'features',
        'feature',
        'enterprise',
        'business',
        'solutions',
        'customers',
        'testimonials',
        'blog',
        'news',
        'press',
        'media',
        'changelog',
        'roadmap',
        'manifesto',
        'mission',
        'vision',
        'docs',
        'documentation',
        'guide',
        'guides',
        'help',
        'support',
        'faq',
        'kb',
        'knowledgebase',
        'tutorials',
        'tutorial',
        'academy',
        'training',
        'onboarding',
        'contact',
        'status',
        'uptime',
        'billing',
        'invoices',
        'invoice',
        'payments',
        'payment',
        'subscription',
        'subscriptions',
        'checkout',
        'upgrade',
        'trial',
        'trials',
        'plans',
        'stripe',
        'paypal',
        'oauth',
        'sso',
        'saml',
        'jwt',
        'token',
        'tokens',
        'verify',
        'verification',
        'reset',
        'password',
        'passwords',
        'forgot-password',
        'magic',
        'magic-link',
        'mfa',
        '2fa',
        'security',
        'cdn',
        'assets',
        'static',
        'uploads',
        'files',
        'storage',
        'cache',
        'queue',
        'queues',
        'worker',
        'workers',
        'websocket',
        'websockets',
        'ws',
        'socket',
        'sockets',
        'cron',
        'scheduler',
        'metrics',
        'logs',
        'monitoring',
        'telemetry',
        'tracing',
        'dev',
        'development',
        'staging',
        'stage',
        'test',
        'testing',
        'qa',
        'sandbox',
        'demo',
        'local',
        'localhost',
        'prod',
        'production',
        'preview',
        'beta',
        'alpha',
        'nightly',
        'canary',
        'www',
        'mail',
        'email',
        'smtp',
        'imap',
        'pop',
        'ftp',
        'sftp',
        'ssh',
        'ns1',
        'ns2',
        'dns',
        'mx',
        'webmail',
        'proxy',
        'gateway',
        'edge',
        'moderator',
        'moderators',
        'operator',
        'operators',
        'staff',
        'employee',
        'employees',
        'backoffice',
        'backend',
        'console',
        'manage',
        'management',
        'control',
        'panel',
        'cpanel',
        'legal',
        'privacy',
        'terms',
        'tos',
        'gdpr',
        'cookies',
        'compliance',
        'licenses',
        'license',
        'security-policy',
        'webhook',
        'webhooks',
        'integrations',
        'integration',
        'connect',
        'sync',
        'import',
        'export',
        'feeds',
        'rss',
        'atom',
        'graphql',
        'openapi',
        'sdk',
        'search',
        'discover',
        'browse',
        'catalog',
        'catalogue',
        'opac',
        'null',
        'undefined',
        'true',
        'false',
        'public',
        'private',
        'default',
        'global',
        'shared',
        'reserved',
        'unknown',
        'anonymous',
        'guest',
        'all',
        'any',
        'none',
        'system',
        'owner',
        'notifications',
        'inbox',
        'messages',
        'chat',
        'activity',
        'analytics',
        'reports',
        'audit',
        'audits',
        'events',
        'tasks',
        'calendar',
        'calendars',
        'mobile',
        'ios',
        'android',
        'desktop',
        'electron',
        'appstore',
        'playstore',
        'ai',
        'assistant',
        'bot',
        'bots',
        'automation',
        'automations',
        'workflows',
        'en',
        'fr',
        'es',
        'de',
        'it',
        'pt',
        'nl',
        'ja',
        'ko',
        'zh',
        'ru',
        'a',
        'i',
        'me',
        'u',
        'x',
        'y',
        'z',
    ],
];
