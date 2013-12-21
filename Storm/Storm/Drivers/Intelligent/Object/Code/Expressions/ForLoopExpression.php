<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ForLoopExpression extends BodiedExpression {
    private $HasFirstExpression;
    private $FirstExpression;
    private $HasSecondExpression;
    private $SecondExpression;
    private $HasThirdExpression;
    private $ThirdExpression;
    
    public function __construct(Expression $BodyExpression, 
            StatementExpression $FirstExpression = null, StatementExpression $SecondExpression = null, 
            StatementExpression $ThirdExpression = null) {
        parent::__construct($BodyExpression);
        $this->HasFirstExpression = $FirstExpression !== null;
        $this->FirstExpression = $FirstExpression;
        
        $this->HasSecondExpression = $SecondExpression !== null;
        $this->SecondExpression = $SecondExpression;
        
        $this->HasThirdExpression = $ThirdExpression !== null;
        $this->ThirdExpression = $ThirdExpression;
    }
    
    public function HasFirstExpression() {
        return $this->HasFirstExpression;
    }

    /**
     * @return StatementExpression
     */
    public function GetFirstExpression() {
        return $this->FirstExpression;
    }

    public function HasSecondExpression() {
        return $this->HasSecondExpression;
    }

    /**
     * @return StatementExpression
     */
    public function GetSecondExpression() {
        return $this->SecondExpression;
    }

    public function HasThirdExpression() {
        return $this->HasThirdExpression;
    }
    
    /**
     * @return StatementExpression
     */
    public function GetThirdExpression() {
        return $this->ThirdExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'for(';
        
        if($this->HasFirstExpression)
            $Code .= $this->FirstExpression->Compile();
        else
            $Code .= ';';
        
        if($this->HasSecondExpression)
            $Code .= $this->SecondExpression->Compile();
        else
            $Code .= ';';
        
        if($this->HasThirdExpression)
            $Code .= $this->ThirdExpression->Compile();
        else
            $Code .= ';';
        
        $Code .= ')';
        
        $Code .= $this->GetBodyExpression()->Compile();
    }
}

?>