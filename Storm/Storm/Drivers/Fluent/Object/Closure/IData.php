<?php

namespace Storm\Drivers\Fluent\Object\Closure;

interface IData {
    /**
     * @return IData
     */
    public function Read(\Closure $Closure);
}

?>
