<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class MethodExpression extends Expression {
    private $SignatureExpression;
    private $HasBodyExpression;
    private $BodyExpression;
    
    public function __construct(MethodSignatureExpression $SignatureExpression, BlockExpression $BodyExpression = null) {
        $this->SignatureExpression = $SignatureExpression;
        $this->HasBodyExpression = $BodyExpression !== null;
        $this->BodyExpression = $BodyExpression;
    }
    
    /**
     * @return MethodSignatureExpression
     */
    public function GetSignatureExpression() {
        return $this->SignatureExpression;
    }

    public function HasBodyExpression() {
        return $this->HasBodyExpression;
    }

    /**
     * @return BlockExpression
     */
    public function GetBodyExpression() {
        return $this->BodyExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= $this->SignatureExpression->Compile();
        if($this->HasBodyExpression) {
            $Code .= $this->BodyExpression->Compile();
        }
    }

}

?>