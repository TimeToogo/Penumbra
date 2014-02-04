<?php

namespace Storm\Tests;

date_default_timezone_set('Australia/Melbourne');
error_reporting(-1);
ini_set('display_errors', 'On');

$IsManuallyLoaded = defined('STORM_ROOT_PATH');
$StormAsProjectAutoLoaderPath = __DIR__ . '/../../../vendor/autoload.php';
$StormAsDependencyAutoLoaderPath = __DIR__ . '/../../../../../../autoload.php';

if($IsManuallyLoaded) {
    $RequiredDependencies = [
        'PHPUnit_Framework_TestCase' => 'PHPUnit'
    ];

    foreach ($RequiredDependencies as $RequiredClass => $DependencyName) {
        if(!class_exists($RequiredClass)) {
            throw new \Exception('Unloaded storm test dependency: ' . $DependencyName);
        }
    }

    function StormTestsAutoLoader($ClassName) {
        if(strpos($ClassName, __NAMESPACE__) !== 0) {
            return;
        }
        
        $FilePath = STORM_ROOT_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $ClassName) . '.php';
        if(file_exists($FilePath)) {
            require_once $FilePath;
        }
    }
    
    spl_autoload_register(__NAMESPACE__ . 'StormTestsAutoLoader');
    return;
}
else if(file_exists($StormAsProjectAutoLoaderPath)) {
    $ComposerAutoLoader = require $StormAsProjectAutoLoaderPath;
}
else if(file_exists($StormAsDependencyAutoLoaderPath)) {
    $ComposerAutoLoader = require $StormAsDependencyAutoLoaderPath;
}
else {
    throw new \Exception('Cannot load storm tests: Storm cannot be loaded, please load Storm via composer or manually if required.');
}

$ComposerAutoLoader->add(__NAMESPACE__, __DIR__ . '/../../');

?>