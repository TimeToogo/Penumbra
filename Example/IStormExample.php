<?php

namespace StormExamples;

use \Storm\Api\Base\Storm;
use \Storm\Api\Base\Repository;

interface IStormExample {
    public function GetStorm();
    public function Run(Storm $Storm);
}

?>