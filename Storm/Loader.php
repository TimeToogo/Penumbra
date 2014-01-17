<?php

define('STORM_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
function StormAutoLoader($ClassName) {
    if(strpos($ClassName, 'Storm') !== 0) {
        return;
    }
    
    $FilePath = STORM_ROOT_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $ClassName) . '.php';
    if(file_exists($FilePath)) {
        require_once $FilePath;
    }
}
spl_autoload_register('StormAutoLoader');

?>
