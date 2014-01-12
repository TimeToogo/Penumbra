<?php

namespace Storm\Drivers\Intelligent\Object\Closure\Implementation\File;

use \Storm\Drivers\Intelligent\Object\Closure\IReader;

class Reader implements IReader {
    
    public function Read(\Closure $Closure) {
        return new Data($Closure);
    }
}

?>
