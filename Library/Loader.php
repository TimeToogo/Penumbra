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
function PenumbraAutoLoader($ClassName) {
    if(strpos($ClassName, __NAMESPACE__) !== 0) {
        return;
    }
    
    $FilePath = PENUMBRA_ROOT_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $ClassName) . '.php';
    if(file_exists($FilePath)) {
        require_once $FilePath;
    }
}
spl_autoload_register(__NAMESPACE__ . '\\PenumbraAutoLoader');

?>
