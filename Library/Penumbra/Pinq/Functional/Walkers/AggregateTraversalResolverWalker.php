<?php

namespace Penumbra\Pinq\Functional\Walkers;

use \Penumbra\Core\Object;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Core\Object\Expressions\Aggregates as A;
use \Penumbra\Pinq\Expressions\AggregateVariableExpression;
use \Penumbra\Pinq\Expressions\EntityVariableExpression;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class AggregateTraversalResolverWalker extends O\ExpressionWalker {
    
    public function WalkMethodCall(O\MethodCallExpression $Expression) {
        if(!$Expression->OriginatesFrom(AggregateVariableExpression::GetType())) {
            return parent::WalkMethodCall($Expression);
        }
        
        $AggregregateVariable = $Expression->GetValueExpression();
        $NameExpression = $Expression->GetNameExpression();
        $ArgumentExpressions = $Expression->GetArgumentExpressions();
        
        if(!($AggregregateVariable instanceof AggregateVariableExpression)
                || !($NameExpression instanceof O\ValueExpression)) {
            return parent::WalkMethodCall($Expression);
        }
        
        $Name = $NameExpression->GetValue();
        
        switch ($Name) {
            case 'First':
                return new EntityVariableExpression();
                
            case 'Count':
                return A\AggregateExpression::Count();
                
            case 'Maximum':
                return A\AggregateExpression::Maximum($this->ParseClosureArgument($ArgumentExpressions));
                
            case 'Minimum':
                return A\AggregateExpression::Minimum($this->ParseClosureArgument($ArgumentExpressions));
                
            case 'Sum':
                return A\AggregateExpression::Sum(false, $this->ParseClosureArgument($ArgumentExpressions));
                
            case 'Average':
                return A\AggregateExpression::Average(false, $this->ParseClosureArgument($ArgumentExpressions));
                
            case 'All':
                return A\AggregateExpression::All($this->ParseClosureArgument($ArgumentExpressions));
                
            case 'Any':
                return A\AggregateExpression::Any($this->ParseClosureArgument($ArgumentExpressions));
            
            case 'Implode':
                return A\AggregateExpression::Implode(
                        false, 
                        $this->ParseArgument($ArgumentExpressions, O\ValueExpression::GetType(), 0)->GetValue(),
                        $this->ParseClosureArgument($ArgumentExpressions, 1));

            default:
                break;
        }
    }
    
    private function ParseClosureArgument(array $ArgumentExpressions, $Index = 0) {
        $ClosureExpression = $this->ParseArgument($ArgumentExpressions, O\ClosureExpression::GetType(), $Index);
        return $this->ParseClosureReturnExpression($ClosureExpression);
    }
    
    private function ParseArgument(array $ArgumentExpressions, $Type, $Index = 0) {
        $ArgumentExpressions = array_values($ArgumentExpressions);
        if(!isset($ArgumentExpressions[$Index])) {
            throw \Penumbra\Pinq\PinqException::UnresolvableAggregateTraversal();
        }
        if(!($ArgumentExpressions[$Index] instanceof $Type)) {
            throw \Penumbra\Pinq\PinqException::UnresolvableAggregateTraversal();
        }
        
        return $ArgumentExpressions[$Index];
    }
    
    private function ParseClosureReturnExpression(O\ClosureExpression $ClosureExpression) {
        $ParameterNames = $ClosureExpression->GetParameterNames();
        if(count($ParameterNames) !== 1) {
            throw \Penumbra\Pinq\PinqException::UnresolvableAggregateTraversal();
        }
        $EntityVariableName = reset($ParameterNames);
        
        $BodyExpressions = $this->WalkAll($ClosureExpression->GetBodyExpressions());
        $ExpressionTree = new \Penumbra\Pinq\Functional\ExpressionTree($BodyExpressions);
        
        if(!$ExpressionTree->HasReturnExpression()
                || !$ExpressionTree->GetReturnExpression()->HasValueExpression()) {
            throw \Penumbra\Pinq\PinqException::UnresolvableAggregateTraversal();
        }
        
        $ExpressionTree->ResolveVariablesToExpressions([$EntityVariableName => new EntityVariableExpression()]);
        
        return $ExpressionTree->GetReturnExpression()->GetValueExpression();
    }   
    
}

?>