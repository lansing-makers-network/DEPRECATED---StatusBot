<?php

// EXTREMELY basic autoloader that will search this directory for
// a PHP file that matches the class name.
spl_autoload_register(function ($class) {
    include $class . '.php';
});

?>