<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class CatchExpression extends BodiedExpression {
    private $ExceptionParameterExpression;
    
    public function __construct(ParameterExpression $ExceptionParameterExpression, BlockExpression $BodyExpression) {
        parent::__construct($BodyExpression);
        $this->ExceptionParameterExpression = $ExceptionParameterExpression;
    }
    
    /**
     * @return ParameterExpression
     */
    public function GetExceptionParameterExpression() {
        return $this->ExceptionParameterExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'catch (' . $this->ExceptionParameterExpression->Compile() . ')';
        $Code .= $this->GetBodyExpression()->Compile();
    }
}

?>