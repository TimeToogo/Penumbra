<?php

namespace Storm\Drivers\Intelligent\Object\Closure;

interface IReader {
    /**
     * @return IData
     */
    public function Read(\Closure $Closure);
}

?>
