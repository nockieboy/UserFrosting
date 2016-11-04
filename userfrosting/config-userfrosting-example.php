<?php

    // Set your timezone here
    date_default_timezone_set('America/New_York');

    // Do not send fatal errors to the response body!
    ini_set("display_errors", "off");

    // Automagically set $environmentVar to 'dev' or 'production'
    // You will need to change this if your dev site isn't on the local computer
    $environmentVar = $_SERVER['SERVER_ADDR'] === '127.0.0.1' ? 'dev' : 'production';

    /* Instantiate the Slim application */
    $app = new \UserFrosting\UserFrosting([
        'view' =>           new \Slim\Views\Twig(),
        'mode' =>           $environmentVar
    ]);

    // Get file path to public directory for this website.  Is this guaranteed to work in all environments?
    $public_path = $_SERVER['DOCUMENT_ROOT'] . $app->environment()['SCRIPT_NAME'];

    // Construct public URL (e.g. "http://www.userfrosting.com/admin").  Feel free to hardcode this if you feel safer.
    $environment = $app->environment();
    $serverport = (($environment['SERVER_PORT'] == 443) or ($environment['SERVER_PORT'] == 80)) ? '' : ':' . $environment['SERVER_PORT'];
    $uri_public_root = $environment['slim.url_scheme'] . "://" . $environment['SERVER_NAME'] . $serverport . $environment['SCRIPT_NAME'];

    /********* DEVELOPMENT SETTINGS *********/
    $app->configureMode('dev', function () use ($app, $public_path, $uri_public_root) {
        $app->config([
            'log.enable' => true,
            'debug' => false,
            'base.path'     => __DIR__,
            'templates.path' => __DIR__ . '/templates',     // This will be overridden anyway by the default theme.
            'themes.path'    =>  __DIR__ . '/templates/themes',
            'plugins.path' => __DIR__ . '/plugins',
            'schema.path' =>    __DIR__ . '/schema',
            'locales.path' =>   __DIR__ . '/locale',
            'log.path' =>   __DIR__ . '/log',
            'public.path' => $public_path,
            'js.path.relative' => "/js",
            'css.path.relative' => "/css",
            'session' => [
                'name' => 'UserFrosting',
                'cache_limiter' => false
            ],
            'db'            =>  [
                'db_host'  => 'localhost',
                'db_port'  => '', // Leave blank to use the default port for your database driver (eg. 3306 for MySQL)
                'db_name'  => 'userfrosting',
                'db_user'  => 'admin',
                'db_pass'  => 'password',
                'db_prefix'=> 'uf_'
            ],
            'mail' => 'smtp',
            'smtp'  => [
                'host' => 'mail.example.com',
                'port' => 465,
                'auth' => true,
                'secure' => 'ssl',
                'user' => 'relay@example.com',
                'pass' => 'password'
            ],
            'uri' => [
                'public'            => $uri_public_root,
                'js-relative'       => "/js",
                'css-relative'      => "/css",
                'favicon-relative'  => "/css/favicon.ico",
                'image-relative'    => "/images"
            ],
            'user_id_guest'  => 0,
            'user_id_master' => 1,
            'theme-base'     => "default",
            'theme-root'     => "root"
        ]);
    });

    /********* PRODUCTION SETTINGS *********/
    $app->configureMode('production', function () use ($app, $public_path, $uri_public_root) {
        $app->config([
            'log.enable' => true,
            'debug' => false,
            'base.path'     => __DIR__,
            'templates.path' => __DIR__ . '/templates',
            'themes.path'    =>  __DIR__ . '/templates/themes',
            'plugins.path' => __DIR__ . '/plugins',
            'schema.path' =>    __DIR__ . '/schema',
            'locales.path' =>   __DIR__ . '/locale',
            'log.path' =>   __DIR__ . '/log',
            'public.path' => $public_path,
            'js.path.relative' => "/js",
            'css.path.relative' => "/css",
            'session' => [
                'name' => 'UserFrosting',
                'cache_limiter' => false
            ],
            'db'            =>  [
                'db_host'  => 'localhost',
                'db_port'  => '', // Leave blank to use the default port for your database driver (eg. 3306 for MySQL)
                'db_name'  => 'userfrosting',
                'db_user'  => 'admin',
                'db_pass'  => 'password',
                'db_prefix'=> 'uf_'
            ],
            'mail' => 'smtp',
            'smtp'  => [
                'host' => 'mail.example.com',
                'port' => 465,
                'auth' => true,
                'secure' => 'ssl',
                'user' => 'relay@example.com',
                'pass' => 'password'
            ],
            'uri' => [
                'public'            => $uri_public_root,
                'js-relative'       => "/js",
                'css-relative'      => "/css",
                'favicon-relative'  => "/css/favicon.ico",
                'image-relative'    => "/images"
            ],
            'user_id_guest'  => 0,
            'user_id_master' => 1,
            'theme-base'     => "default",
            'theme-root'     => "root"
        ]);
    });

    // Set up derived configuration values
    $app->config([
        'js.path' =>  $app->config('public.path') . $app->config('js.path.relative'),
        'css.path' => $app->config('public.path') . $app->config('css.path.relative'),
        'uri' => [
            'js' =>        $app->config('uri')['public'] . $app->config('uri')['js-relative'],
            'css' =>       $app->config('uri')['public'] . $app->config('uri')['css-relative'],
            'favicon' =>   $app->config('uri')['public'] . $app->config('uri')['favicon-relative'],
            'image' =>     $app->config('uri')['public'] . $app->config('uri')['image-relative'],
        ]
    ], true);
