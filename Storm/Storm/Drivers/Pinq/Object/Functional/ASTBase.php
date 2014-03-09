<?php

namespace Storm\Drivers\Pinq\Object\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\IProperty;

abstract class ASTBase implements IAST {    
    protected $EntityVariableName;
    
    public function __construct($EntityVariableName) {
        $this->EntityVariableName = $EntityVariableName;
    }
    
    final public function GetEntityVariableName() {
        return $this->EntityVariableName;
    }
}

?>
