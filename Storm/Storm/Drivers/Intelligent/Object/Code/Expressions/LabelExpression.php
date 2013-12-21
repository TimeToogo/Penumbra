<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class LabelExpression extends Expression {
    private $LabelName;
    public function __construct($LabelName) {
        $this->LabelName = $LabelName;
    }
    
    public function GetLabelName() {
        return $this->LabelName;
    }

        
    protected function CompileCode(&$Code) {
        $Code .= $this->LabelName . ': ';
    }
}

?>