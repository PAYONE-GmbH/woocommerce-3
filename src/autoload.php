<?php

/**
 * PSR-4 Autoloader from http://www.php-fig.org/psr/psr-4/examples/
 */

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Payone';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});