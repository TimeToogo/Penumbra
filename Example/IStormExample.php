<?php

namespace StormExamples;

use \Storm\Api\Base\Storm;
use \Storm\Api\Base\Repository;

interface IStormExample {
    /**
     * @return Storm
     */
    public function GetStorm();
    public function Run(Storm $Storm);
}

?>