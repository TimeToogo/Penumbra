<?php

namespace Storm;

$RequiredDependencies = [
    'PHPParser_Parser' => 'nikic/PHPParser'
];

foreach ($RequiredDependencies as $RequiredClass => $DependencyName) {
    if(!class_exists($RequiredClass)) {
        throw new Core\StormException('Cannot load Storm: unloaded storm dependency - %s', $DependencyName);
    }
}

define('STORM_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
function StormAutoLoader($ClassName) {
    if(strpos($ClassName, __NAMESPACE__) !== 0) {
        return;
    }
    
    $FilePath = STORM_ROOT_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $ClassName) . '.php';
    if(file_exists($FilePath)) {
        require_once $FilePath;
    }
}
spl_autoload_register(__NAMESPACE__ . '\\StormAutoLoader');

?>
