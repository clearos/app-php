<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'php';
$app['version'] = '2.1.6';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('php_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('php_app_name');
$app['category'] = lang('base_category_server');
$app['subcategory'] = lang('base_subcategory_web');
$app['menu_enabled'] = FALSE;

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_only'] = TRUE;

$app['core_requires'] = array(
    'app-date-core >= 1:1.6.2',
    'app-web-server-core',
    'php >= 5.3.3',
    'php-gd >= 5.3.3',
    'php-ldap >= 5.3.3',
    'php-mbstring >= 5.3.3',
    'php-mysql >= 5.3.3',
    'php-process >= 5.3.3',
    'php-soap >= 5.3.3',
    'php-xml >= 5.3.3',
);

$app['core_file_manifest'] = array(
    'php.conf' => array (
        'target' => '/etc/clearos/php.conf',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
    'date-event'=> array(
        'target' => '/var/clearos/events/date/php',
        'mode' => '0755'
    ),
);

$app['core_directory_manifest'] = array(
    '/var/clearos/php' => array(),
);
