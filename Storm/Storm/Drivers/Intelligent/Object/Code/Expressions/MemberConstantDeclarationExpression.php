<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class MemberConstantDeclarationExpression extends ConstantDeclarationExpression {
    private $AccessLevel;
    public function __construct($AccessLevel, $Name, $Value) {
        parent::__construct($Name, $Value);
        $this->AccessLevel = $AccessLevel;
    }
    
    public function GetAccessLevel() {
        return $this->AccessLevel;
    }
    
    protected function CompileStatement(&$Code) {
        $Code .= $this->AccessLevel . ' ';
        parent::CompileStatement($Code);
    }
}

?>