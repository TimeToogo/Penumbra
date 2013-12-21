<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class NewArrayExpression extends ValueExpression {
    private $KeyValueExpressions;
    private $ValueExpressions;
    
    public function __construct(array $KeyValueExpressions = array(), array $ValueExpressions = array()) {
        $this->KeyValueExpressions = $KeyValueExpressions;
        $this->ValueExpressions = $ValueExpressions;
    }
    
    /**
     * @return ValueExpression[]
     */
    public function GetKeyValueExpressions() {
        return $this->KeyValueExpressions;
    }
    
    /**
     * @return ValueExpression[]
     */
    public function GetValueExpressions() {
        return $this->ValueExpressions;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'array';
        $Code .= '(';
        foreach($this->ValueExpressions as $Key => $ValueExpression) {
            if(isset($this->KeyValueExpressions[$Key])) {
                $Code .= $this->KeyValueExpressions[$Key]->Compile();
                $Code .= ' => ';
            }
            $Code .= $ValueExpression->Compile();
            $Code .= ', ';
        }
        $Code = substr($Code, 0, -2);
        $Code .= ')';
    }
}

?>