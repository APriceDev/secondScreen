<?php
$config = array();
// Determine prod/dev
if (isset($_SERVER['HTTP_HOST'])) {
    // Disregard port.
    $host = explode(':', strtolower($_SERVER['HTTP_HOST']))[0];
    switch ($host) {
        case 'ss.sobellaenterprises.com':
            $config['ENV'] = 'prod';
            $config['DEBUG'] = false;
            $include = "$system/config/prod.php";
            break;
        case 'dev-ss.sobellaenterprises.com':
        case 'fe-ss.sobellaenterprises.com':
            $config['ENV'] = 'dev';
            $config['DEBUG'] = true;
            $include = "$system/config/dev.php";
            break;
        default:
            $config['ENV'] = 'dev';
            $config['DEBUG'] = true;
            $include = "$system/config/dev.php";
        break;
    }
} else {
    // command line script most likely
    if (isset($dev) && $dev) {
        $_SERVER['HTTP_HOST'] = '54.221.211.170';
        $config['ENV'] = 'dev';
        $config['DEBUG'] = true;
        $include = "$system/config/dev.php";
    } else { 
        // cron job most likely, assume prod
        $_SERVER['HTTP_HOST'] = 'ss.sobellaenterprises.com';
        $config['ENV'] = 'prod';
        $config['DEBUG'] = false;
        $include = "$system/config/prod.php";
    }
}

// Load remaining shared config values

// Default controller
$config['default_controller'] = 'Index';

// Mail settings
$config['mail'] = array(
    'transport' => 'phpmail',
);

$config['email_templates_path'] = "$system/app/emails/";

/**
 * E-mail
 */
$config['email_settings'] = array(
    'server' => 'sobellaenterprises.com',
    'from_name' => "Second Screen Notifications",
    'from_addr' => "notifications@sobellaenterprises.com",
);

$config['email_templates_path'] = "$system/app/emails/";

$config['project_name'] = 'Second Screen';

/**
 * S3
 */
$config['s3_bucket'] = 'videos.ss'; 

/**
 * AWS
 */
$config['aws_access_key'] = 'AKIAIHUGMJAZAHJAE7JA';
$config['aws_secret_key'] = '0B2NbZsUy8BvsfU3spVLDRr9+N5omNTUaUpks63q';

// Routing
$config['routing'] = array(
    'replace' => array(
        // replacements
        '{:action}'     => '([a-z-]+)',
        '{:alpha}'      => '([a-zA-Z]+)',
        '{:alnum}'      => '([a-zA-Z0-9]+)',
        '{:controller}' => '([a-z-]+)',
        '{:digit}'      => '([0-9]+)',
        '{:param}'      => '([^/]+)',
        '{:params}'     => '(.*)',
        '{:slug}'       => '([a-zA-Z0-9-]+)',
        '{:word}'       => '([a-zA-Z0-9_]+)',
    ),
    'rewrite' => array(    
        'admin/groups/new' => 'admin/new-group',
        'admin/groups/edit/{:digit}' => 'admin/edit-group/$1',
        'admin/permissions/new' => 'admin/new-permission',
        'admin/permissions/edit/{:digit}' => 'admin/edit-permission/$1',
        'admin/users/new' => 'admin/new-user',
        'admin/users/edit/{:digit}' => 'admin/edit-user/$1',
        'admin/series/new' => 'admin/new-series',
        'admin/series/quick-add' => 'admin/quick-add',
        'admin/series/edit/{:digit}' => 'admin/edit-series/$1',
        'admin/series/{:digit}/seasons' => 'admin/seasons/$1',
        'admin/series/{:digit}/seasons/new' => 'admin/new-season/$1',
        'admin/series/{:digit}/seasons/edit/{:digit}' => 'admin/edit-season/$1/$2',
        'admin/series/{:digit}/seasons/{:digit}/episodes' => 'admin/episodes/$1/$2',
        'admin/series/{:digit}/seasons/{:digit}/episodes/new' => 'admin/new-episode/$1/$2',
        'admin/series/{:digit}/seasons/{:digit}/episodes/edit/{:digit}' => 'admin/edit-episode/$1/$2/$3',
    )
);

// Load dev/prod specific config values (Included here to allow for overriding previous values)
include $include;
