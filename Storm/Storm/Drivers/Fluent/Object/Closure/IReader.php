<?php

namespace Storm\Drivers\Fluent\Object\Closure;

interface IReader {
    /**
     * @return IData
     */
    public function Read(\Closure $Closure);
}

?>
