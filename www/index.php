<?php

defined('APP_NAME') || define('APP_NAME', 'Veni, Vidi, Vici!');
// INDEX_PATH points to the public directory
define('INDEX_PATH', realpath(dirname(__FILE__) ));

// BASE_PATH points to the root directory
define('BASE_PATH', realpath(dirname(__FILE__) . '/..'));
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
$conf  = parse_ini_file(APPLICATION_PATH . '/configs/application.ini',null);
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define('APPLICATION_DOWN',$conf['down']);
define('APPLICATION_DOMAIN',$conf['domain']);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../views/scripts'),
    get_include_path(),
)
));

/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
        
