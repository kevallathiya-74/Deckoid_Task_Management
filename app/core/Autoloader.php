<?php

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $segments = explode('\\', $relative_class);
    if (count($segments) > 1) {
        for ($i = 0; $i < count($segments) - 1; $i++) {
            $segments[$i] = strtolower($segments[$i]);
        }
    }

    $file = $base_dir . implode('/', $segments) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
