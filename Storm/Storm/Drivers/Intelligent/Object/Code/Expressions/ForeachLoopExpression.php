<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ForeachLoopExpression extends BodiedExpression {
    private $TraversableValueExpression;
    private $HasKey;
    private $KeyValueExpression;
    private $AsValueExpression;
    
    public function __construct(
            ValueExpression $TraversableValueExpression,  
            ValueExpression $AsValueExpression, 
            Expression $BodyExpression,
            ValueExpression $KeyValueExpression = null) {
        parent::__construct($BodyExpression);
        $this->TraversableValueExpression = $TraversableValueExpression;
        $this->AsValueExpression = $AsValueExpression;
        $this->HasKey = $KeyValueExpression !== null;
        $this->KeyValueExpression = $KeyValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetTraversableValueExpression() {
        return $this->TraversableValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetAsValueExpression() {
        return $this->AsValueExpression;
    }
    
    public function HasKey() {
        return $this->HasKey;
    }

    /**
     * @return ValueExpression
     */
    public function GetKeyValueExpression() {
        return $this->KeyValueExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'foreach(' . $this->TraversableValueExpression->Compile() . ' ';
        if($this->HasKey) {
            $Code .= $this->KeyValueExpression->Compile() . ' => ';
        }
        $Code .= 'as ' . $this->AsValueExpression->Compile() . ')';
        $Code .= $this->GetBodyExpression()->Compile();
    }

}

?>