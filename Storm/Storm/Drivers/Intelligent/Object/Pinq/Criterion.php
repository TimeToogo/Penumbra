<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Constant\Object\EntityMap;
use \Storm\Drivers\Intelligent\Object\Pinq\Closure\Reader;
use \Storm\Drivers\Intelligent\Object\Code\Parsing\Parser;


class Criterion extends Object\Criterion {
    private $EntityMap;
    
    public function __construct(EntityMap $EntityMap) {
        parent::__construct();
        
        $this->EntityMap = $EntityMap;
    }
    
    public function Where(\Closure $PredicateClosure) {
        $this->AddPredicate($this->ParseReturnExpressionClosure($PredicateClosure));
        
        return $this;
    }
    
    
    public function OrderBy(\Closure $ExpressionClosure) {
        $this->AddOrderByExpression($this->ParseReturnExpressionClosure($ExpressionClosure), true);
        
        return $this;
    }
    
    public function OrderByDescending(\Closure $ExpressionClosure) {
        $this->AddOrderByExpression($this->ParseReturnExpressionClosure($ExpressionClosure), false);
        
        return $this;
    }
    
    public function Skip($Amount) {
        $this->SetRangeOffset($Amount);
        
        return $this;
    }
    
    public function Limit($Amount) {
        $this->SetRangeAmount($Amount);
        
        return $this;
    }
    
    private function ParseReturnExpressionClosure(\Closure $Closure) {
        $ClosureReader = new Reader($Closure);
        $Parameters = $ClosureReader->GetParameters();
        if(count($Parameters) !== 1) {
            throw new \Exception();
        }
        
        
        $EntityVariableName = $Parameters[0]->getName();
        $UsedVariableMap = $ClosureReader->GetUsedVariablesMap();
        
        $CodeParser = new Parser();
        $BodyAST = $CodeParser->Parse($ClosureReader->GetBodySource(), $UsedVariableMap);
        
        //$Printer = new \PHPParser_PrettyPrinter_Default();
        //echo $Printer->prettyPrint($BodyAST);
        
        $ReturnExpression = null;
        foreach ($BodyAST as $Key => $StatementNode) {
            if($StatementNode instanceof \PHPParser_Node_Stmt_Return) {
                $ReturnExpression = $StatementNode;
                unset($BodyAST[$Key]);
            }
        }
        if($ReturnExpression === null) {
            throw new \Exception('Closure must return value');
        }
        
        $RuleExpression = $CodeParser->ParseExpressionNode($this->EntityMap, $EntityVariableName, true, $ReturnExpression->expr);
        
        return $RuleExpression;
    }
}

?>
