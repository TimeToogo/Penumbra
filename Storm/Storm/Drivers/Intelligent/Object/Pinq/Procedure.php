<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Constant\Object\EntityMap;
use \Storm\Drivers\Intelligent\Object\Pinq\Closure\Reader;
use \Storm\Drivers\Intelligent\Object\Code\Parsing\Parser;


class Procedure extends Object\Procedure {
    private $EntityMap;
    
    public function __construct(EntityMap $EntityMap, \Closure $ProcedureClosure) {
        $AssignmentExpressions = $this->ParseAssignmentExpressions($EntityMap, $ProcedureClosure);
        
        parent::__construct($EntityMap->GetEntityType(), $AssignmentExpressions, new Criterion($EntityMap));
        
        $this->EntityMap = $EntityMap;
    }  
    
    private function ParseAssignmentExpressions(EntityMap $EntityMap, \Closure $Closure) {
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
        
        $AssignmentExpressions = array();
        foreach ($BodyAST as $StatementNode) {
            if(strpos($StatementNode->getType(), 'Expr_Assign') === 0) {
                
                $AssignmentExpression = $CodeParser->ParseExpressionNode($EntityMap, $EntityVariableName, true, $StatementNode);
                
                if($AssignmentExpression->GetLeftOperandExpression() instanceof \Storm\Core\Object\Expressions\PropertyExpression) {
                    $AssignmentExpressions[] = $AssignmentExpression;
                }
            }
        }
        
        return $AssignmentExpressions;
    }
    
    /**
     * @return Criterion
     */
    public function GetCriterion() {
        return parent::GetCriterion();
    }
}

?>
