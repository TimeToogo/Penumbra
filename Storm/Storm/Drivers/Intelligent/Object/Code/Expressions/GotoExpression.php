<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class GotoExpression extends StatementExpression {
    private $LabelName;
    public function __construct($LabelName) {
        $this->LabelName = $LabelName;
    }
    
    public function GetLabelName() {
        return $this->LabelName;
    }

        
    protected function CompileStatement(&$Code) {
        $Code .= 'goto ' . $this->LabelName;
    }

}

?>