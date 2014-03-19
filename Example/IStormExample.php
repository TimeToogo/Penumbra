<?php

namespace StormExamples;

use \Storm\Api\Base\Storm;
use \Storm\Api\Base\EntityManager;

interface IStormExample {
    /**
     * @return Storm
     */
    public function GetStorm();
    public function Run(Storm $Storm);
}

?>