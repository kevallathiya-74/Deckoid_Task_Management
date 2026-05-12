<?php

/**
 * URL Helper
 */
function url($path = '')
{
    $baseUrl = env('APP_URL', 'http://localhost/Task_Management');
    $path = ltrim($path, '/');
    return rtrim($baseUrl, '/') . ($path ? '/' . $path : '');
}

function asset($path)
{
    return url('public/' . ltrim($path, '/'));
}

function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}
