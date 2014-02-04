<?php

namespace Storm\Tests;

require_once 'TestBootstrapper.php';

$argv = array(
    '--configuration', 'Configuration.xml',
    './',
);
$_SERVER['argv'] = $argv;

\PHPUnit_TextUI_Command::main();
?>