<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class NamespaceExpression extends Expression {
    private $Namespace;
    private $HasBody;
    private $BodyExpression;
    public function __construct($Namespace, BlockExpression $BodyExpression = null) {
        $this->Namespace = $Namespace;
        $this->HasBody = $BodyExpression !== null;
        $this->BodyExpression = $BodyExpression;
    }
    
    public function GetNamespace() {
        return $this->Namespace;
    }
        
    final public function HasBody() {
        return $this->HasBody;
    }
    /**
     * @return Exprssion
     */
    final public function GetBodyExpression() {
        return $this->BodyExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'namespace ' . $this->Namespace;
        if($this->HasBody) {
            $Code .= $this->BodyExpression->Compile();
        }
        else {
            $Code .= ';';
        }
    }
}

?>