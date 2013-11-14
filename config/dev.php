<?php
/**
 * ini_set values
 */
$config['ini_set'] = array(
    'error_reporting'      => (E_ALL | E_STRICT),
    'display_errors'       => true,
    'html_errors'          => true,
    'session.save_path'    => "tcp://localhost:11211?persistent=1&amp;weight=1&amp;timeout=1&amp;retry_interval=15",
    'session.save_handler' => 'memcache',
    'session.cookie_domain' => '.sobellaenterprises.com',
    'date.timezone'        => 'America/New_York',
);

/**
 * default app database
 */
$config['database'] = array(
    'type' => 'mysql', // mysql, postgres
    'host' => 'localhost',
    'user' => 'secondscreendev',
    'pass' => 'Tyz@SVz26SY7W!',
    'name' => 'secondscreendev',
    'port' => 3306,
    'cache' => array(
        'type' => 'memcache', // memcache
        'host' => 'localhost',
        'port' => 11211,
    )
);

// Internal API keys
$config['ss_api_internal_sk'] = array(
    'agentile',
    'jenrenx',
    'apricedevx',
    'dedual',
);
