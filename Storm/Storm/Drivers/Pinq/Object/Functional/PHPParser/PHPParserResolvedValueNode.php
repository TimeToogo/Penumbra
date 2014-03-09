<?php

namespace Storm\Drivers\Pinq\Object\Functional\PHPParser;

class PHPParserResolvedValueNode extends \PHPParser_Node_Expr {
    
    public function __construct(&$Value) {
        parent::__construct(
                [
                    'Value' => &$Value
                ], 
                []);
    }
}

?>
