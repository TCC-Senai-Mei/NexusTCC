<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 * --------------------------------------------------------------------
 * CodeIgniter 4 Front Controller (public/index.php)
 * --------------------------------------------------------------------
 */

// Path to the front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure current directory is set correctly
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// Load our paths config
require FCPATH . '../app/Config/Paths.php';

$paths = new Paths();

// Load the framework bootstrapping file
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));

