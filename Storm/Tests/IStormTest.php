<?php

namespace StormTests;

use \Storm\Core\Storm;

interface IStormTest {
    public function GetStorm();
    public function Run(Storm $Storm);
}

?>