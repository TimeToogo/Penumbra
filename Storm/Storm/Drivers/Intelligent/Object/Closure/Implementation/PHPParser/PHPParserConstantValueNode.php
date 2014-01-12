<?php

namespace Storm\Drivers\Intelligent\Object\Pinq\Closure\Implementation\PHPParser;


class PHPParserConstantValueNode extends \PHPParser_NodeAbstract {
    
    public function __construct($Value) {
        parent::__construct(
                array(), 
                [
                    'Value' => $Value
                ]);
    }
}

?>
