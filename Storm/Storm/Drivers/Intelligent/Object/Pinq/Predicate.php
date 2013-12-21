<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Core\Object\Constraints;
use \Storm\Core\Object\EntityMap;

class Predicate extends Constraints\Predicate {
    public function __construct(EntityMap $EntityMap, \Closure $ExpressionClosure) {
        parent::__construct($EntityMap->GetEntityType());
        $ExpressionParser = new Closure\Reader($ExpressionClosure);
        $Parameters = $ExpressionParser->GetParameters();
        if(count($Parameters) === 0)
            throw new Exception;
        
        $EntityVariableName = $Parameters[0]->getName();
        $ExpressionSource = $ExpressionParser->GetBodySource();
        $ExpressionTokens = $this->GetExpressionTokens($ExpressionSource);
        $VariableMap = $ExpressionParser->GetUsedVariables();
        
        $this->AddRules(new RuleGroup($EntityMap, $EntityVariableName, $ExpressionTokens, $VariableMap));
    }
    
    private function GetExpressionTokens($ExpressionSource) {
        $FullSource = '<?php ' . $ExpressionSource . ' ?>';
        $Tokens = token_get_all($FullSource);
        
        array_shift($Tokens);
        array_pop($Tokens);
        $Tokens = array_filter($Tokens, function ($TokenInfo) {
            return is_array($TokenInfo) ? $TokenInfo[0] !== T_WHITESPACE : true;
        });
        
        return $Tokens;
    }
}

?>