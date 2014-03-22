<?php

namespace StormExamples;

?>

<style>
    body, html {
        width: calc(100% - 20px);
        margin: 10px;
        word-wrap:break-word;
    }
</style>
<pre>

<?php
            
date_default_timezone_set('Australia/Melbourne');

ini_set('display_errors', 'On');
error_reporting(-1);
set_time_limit(1000);

define('DIRECTORY_SEPERATOR', '/');
define('BASE_PATH', str_replace('\\', DIRECTORY_SEPERATOR, __DIR__) . DIRECTORY_SEPERATOR);
define('ROOT_NAMESPACE', __NAMESPACE__);

$GLOBALS['LoadCount'] = 0;
$GLOBALS['LoadList'] = [];

function autoload($ClassName) {
    if(strpos($ClassName, ROOT_NAMESPACE) !== 0) {
        return;
    }
    
    $FullClass = '\\' . $ClassName;
    if(class_exists($FullClass) || interface_exists($FullClass)) {
        return;
    }
    
    $ClassName = str_replace(ROOT_NAMESPACE . '\\', '', $ClassName);
    
    $FilePath = BASE_PATH;
    $FilePath .= str_replace('\\', DIRECTORY_SEPERATOR, $ClassName) . '.php';
    
    if(file_exists($FilePath)) {
        require_once $FilePath;
        
        $GLOBALS['LoadCount']++;
        $GLOBALS['LoadList'][] = $FilePath;
    }
}
spl_autoload_register(__NAMESPACE__ . '\autoload');

function ShowTests() {
    foreach(scandir(BASE_PATH) as $Directory) {
        if ($Directory === '.' || $Directory === '..') continue;
        
        if(is_dir($Directory)) {
            echo '<a href=\'' . explode('?', $_SERVER["REQUEST_URI"])[0] . '?Test=' . urlencode($Directory) . '\'>' . $Directory . '</a>';
            echo '<br />';
        }
    }
}

require_once '../vendor/autoload.php';

$Success = false;
if(isset($_GET['Test'])) {
    $TestDir = $_GET['Test'];
    
    $TestFile = BASE_PATH . $TestDir . DIRECTORY_SEPERATOR . 'Example.php';
    var_dump($TestDir);
    if(file_exists($TestFile)) {
        var_dump('Running Test From Dir: ' . $TestDir);
        $TestInstance = require_once $TestFile;
        if($TestInstance instanceof IStormExample)
        {
            require_once 'UBench.php';
            require_once '../Storm/Loader.php';
            
            $Benchmark = new \Ubench();
            
            $Benchmark->start();
            $Storm = $TestInstance->GetStorm();
            $Benchmark->end();
            
            var_dump('Storm');
            var_dump($Benchmark->getTime());
            var_dump($Benchmark->getMemoryPeak());
            
            $Benchmark->start();
            $Result = $TestInstance->Run($Storm);
            $Benchmark->end();
            
            var_dump('Test');
            var_dump($Benchmark->getTime());
            $TimeSpentQuerying = $Storm->GetDomainDatabaseMap()->GetDatabase()->GetPlatform()->GetConnection()->GetTimeSpentQuerying();
            var_dump('Time spent querying: ' . round($TimeSpentQuerying * 1000) . 'ms');
            var_dump('Time spent querying percentage: ' . ($TimeSpentQuerying / $Benchmark->getTime(true) * 100) . '%');
            var_dump($Benchmark->getMemoryPeak());
            $Success = true;
        }
    }
}
if(!$Success)
    ShowTests();

?>