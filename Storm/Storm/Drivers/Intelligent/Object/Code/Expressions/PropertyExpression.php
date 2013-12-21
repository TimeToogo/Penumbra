<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class PropertyExpression extends ValueExpression {
    private $ObjectValueExpression;
    private $NameValueExpression;
    public function __construct(ValueExpression $ObjectValueExpression, ValueExpression $NameValueExpression) {
        parent::__construct();
        
        $this->ObjectValueExpression = $ObjectValueExpression;
        $this->NameValueExpression = $NameValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetObjectValueExpression() {
        return $this->ObjectValueExpression;
    }

    /**
     * @return ValueExpression
     */
    public function GetNameValueExpression() {
        return $this->NameValueExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= $this->ObjectValueExpression->Compile() . '->{' . $this->NameValueExpression->Compile() . '}';
    }
}

?>