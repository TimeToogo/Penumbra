<?php

namespace Penumbra;

$RequiredDependencies = [
    'PHPParser_Parser' => 'nikic/PHPParser'
];

foreach ($RequiredDependencies as $RequiredClass => $DependencyName) {
    if(!class_exists($RequiredClass)) {
        throw new Core\PenumbraException('Cannot load Penumbra: unloaded penumbra dependency - %s', $DependencyName);
    }
}

define('PENUMBRA_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('PENUMBRA_ROOT_NAMESPACE', __NAMESPACE__);

function PenumbraAutoLoader($ClassName) {
    if(strpos($ClassName, PENUMBRA_ROOT_NAMESPACE . '\\') !== 0) {
        return;
    }
    
    $ClassName = substr($ClassName, strlen(PENUMBRA_ROOT_NAMESPACE) + 1);
    $FilePath = PENUMBRA_ROOT_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $ClassName) . '.php';
    if(file_exists($FilePath)) {
        require_once $FilePath;
    }
}

spl_autoload_register(__NAMESPACE__ . '\\PenumbraAutoLoader');

?>
