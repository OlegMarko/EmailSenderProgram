<?php

spl_autoload_register(function ($class) {
    $sourcePath = __DIR__ . DIRECTORY_SEPARATOR;

    $replaceDirectorySeparator = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $filePath = $sourcePath . $replaceDirectorySeparator . '.php';

    if (file_exists($filePath)) {
        require($filePath);
    }
});