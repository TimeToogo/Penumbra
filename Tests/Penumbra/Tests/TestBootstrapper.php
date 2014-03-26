<?php

namespace Penumbra\Tests;

date_default_timezone_set('Australia/Melbourne');
error_reporting(-1);
ini_set('display_errors', 'On');

$IsManuallyLoaded = defined('PENUMBRA_ROOT_PATH');
$PenumbraAsProjectAutoLoaderPath = __DIR__ . '/../../../vendor/autoload.php';
$PenumbraAsDependencyAutoLoaderPath = __DIR__ . '/../../../../../../autoload.php';

if($IsManuallyLoaded) {
    $RequiredDependencies = [
        'PHPUnit_Framework_TestCase' => 'PHPUnit'
    ];

    foreach ($RequiredDependencies as $RequiredClass => $DependencyName) {
        if(!class_exists($RequiredClass)) {
            throw new \Exception('Unloaded penumbra test dependency: ' . $DependencyName);
        }
    }

    function PenumbraTestsAutoLoader($ClassName) {
        if(strpos($ClassName, __NAMESPACE__) !== 0) {
            return;
        }
        
        $FilePath = PENUMBRA_ROOT_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $ClassName) . '.php';
        if(file_exists($FilePath)) {
            require_once $FilePath;
        }
    }
    
    spl_autoload_register(__NAMESPACE__ . 'PenumbraTestsAutoLoader');
    return;
}
else if(file_exists($PenumbraAsProjectAutoLoaderPath)) {
    $ComposerAutoLoader = require $PenumbraAsProjectAutoLoaderPath;
}
else if(file_exists($PenumbraAsDependencyAutoLoaderPath)) {
    $ComposerAutoLoader = require $PenumbraAsDependencyAutoLoaderPath;
}
else {
    throw new \Exception('Cannot load penumbra tests: Penumbra cannot be loaded, please load Penumbra via composer or manually if required.');
}

$ComposerAutoLoader->add(__NAMESPACE__, __DIR__ . '/../../');

?>