<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\File;

use \Storm\Drivers\Fluent\Object\Closure\IReader;

class Reader implements IReader {
    
    public function Read(\Closure $Closure) {
        return new Data($Closure);
    }

}

?>
