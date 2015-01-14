<?php

spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $loadFolderPrefixs = array('', 'lib/'); //load both external lib classes & application classes
    foreach ($loadFolderPrefixs as $folderPrefix) {
        $filePath = $folderPrefix . $fileName;
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
});



require_once 'Config/config.php';