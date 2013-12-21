<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class TryExpression extends BlockBodiedExpression {
    private $CatchExpressions;
    
    public function __construct(BlockExpression $BodyExpression, array $CatchExpressions) {
        parent::__construct($BodyExpression);
        
        $this->CatchExpressions = $CatchExpressions;

    }
    
    /**
     * @return CatchExpression[]
     */
    public function GetCatchExpression() {
        return $this->CatchExpressions;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'try';
        $Code .= $this->GetBodyExpression()->Compile() . ' ';
        $Code .= implode(' ', $this->CatchExpressions);
    }
}

?>