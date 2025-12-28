<?php

// 🔹 Helper immer laden (vor Klassen)
require_once BASE_PATH . '/app/Core/helpers.php';

spl_autoload_register(function (string $class) {

    $paths = [
        BASE_PATH . '/app/Core/' . $class . '.php',
        BASE_PATH . '/app/Controllers/' . $class . '.php',
        BASE_PATH . '/app/Models/' . $class . '.php',
        BASE_PATH . '/app/Services/' . $class . '.php'
    ];

    foreach ($paths as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});
