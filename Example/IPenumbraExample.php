<?php

namespace PenumbraExamples;

use \Penumbra\Api\Base\ORM;
use \Penumbra\Api\Base\EntityManager;

interface IPenumbraExample {
    /**
     * @return ORM
     */
    public function GetPenumbra();
    public function Run(ORM $Penumbra);
}

?>