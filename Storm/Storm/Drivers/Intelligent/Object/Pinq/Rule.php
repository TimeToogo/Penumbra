<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Core\Object\Constraints;
use \Storm\Core\Object\EntityMap;
use \Storm\Drivers\Base\Object\Properties;

class Rule extends Constraints\Rule {
    public function __construct(EntityMap $EntityMap, $EntityVariableName, array $ExpressionTokens, array $VariableMap) {
        list($Rules, $RuleGroups, $IsAllRequired) = 
                $this->ParseExpressionTokens($EntityMap, $EntityVariableName, $ExpressionTokens, $VariableMap);
        
        parent::__construct($Rules, $RuleGroups, $IsAllRequired);
    }
        
    private function ParseRuleTokens(EntityMap $EntityMap, $EntityVariableName, 
            array $RuleTokens, array $VariableMap) {
        list($LeftHandSide, $Comparison, $RightHandSide) = $this->ParseRuleSegments($RuleTokens);
        
        if(count($LeftHandSide) === 0 || $Comparison === null || count($RightHandSide) === 0)
            throw new \Exception;
        
        
        //Special case: contains - strpos
        if(is_array($RightHandSide[0])
                && $Comparison === Constraints\Comparison::NotEquals
                && is_array($RightHandSide[0])) {
            
            $RightValue = array_shift($RightHandSide);
            if($RightValue[0] === T_STRING && strtoupper($RightValue[1]) === 'FALSE') {
                
                $LeftMethodCall = array_shift($LeftHandSide);
                if($LeftMethodCall[0] === T_STRING) {
                    $Comparison = null;
                    if(strtolower($LeftMethodCall[1]) === 'strpos')
                        $Comparison = Constraints\Comparison::Contains;
                    if(strtolower($LeftMethodCall[1]) === 'stripos')
                        $Comparison = Constraints\Comparison::ContainsCaseInsensitive;
                    else
                        throw new \Exception;
                    
                    $ArgumentArray = $this->GetFunctionArgumentTokensArray($LeftHandSide);
                    if(count($ArgumentArray) !== 2)
                        throw new \Exception;
                    
                    $EntityProperty = $this->GetEntityPropertyFromTraverse($EntityMap, $ArgumentArray[0]);
                    $SearchValue = $this->GetVariableValue(array_shift($ArgumentArray[1]), $RuleTokens);
                    
                    return new Constraints\Rule([$EntityProperty], $Comparison, [$SearchValue]);
                }
            }
        }
        
        $EntityProperty = $this->GetEntityPropertyFromTraverse($EntityMap, $LeftHandSide);
        
        $Value = $this->GetVariableValue($RightHandSide, $VariableMap);
        
        return new Constraints\Rule([$EntityProperty], $Comparison, [$Value]);
    }
    private function ParseRuleSegments(array $RuleTokens) {
        $LeftHandSide = array();
        $Comparision = null;
        foreach($RuleTokens as $Key => $Token) {
            $ParsedComparison = $this->GetComparisionFromToken(is_array($Token) ? $Token[0] : $Token);
            if($ParsedComparison !== null) {
                $Comparision = $ParsedComparison;
                unset($RuleTokens[$Key]);
                break;
            }
            else {
                $LeftHandSide[$Key] = $Token;
                unset($RuleTokens[$Key]);
            }
        }
        $RightHandSide = array_values($RuleTokens);
        $RightHandSide = array_filter($RightHandSide, function($Token) {
            return $Token !== ';';
        });
        
        return [$LeftHandSide, $Comparision, $RightHandSide];
    }
    
    private static $TokenComparisons = null;
    private function GetComparisionFromToken($Token) {
        if(self::$TokenComparisons === null) {
            self::$TokenComparisons = array(
                T_IS_EQUAL => Constraints\Comparison::Equals,
                T_IS_IDENTICAL => Constraints\Comparison::Equals,
                
                T_IS_NOT_EQUAL => Constraints\Comparison::NotEquals,
                T_IS_NOT_IDENTICAL => Constraints\Comparison::NotEquals,
                
                '>' => Constraints\Comparison::GreaterThan,
                T_IS_GREATER_OR_EQUAL => Constraints\Comparison::GreaterThanOrEqualTo,
                
                '<' => Constraints\Comparison::LessThan,
                T_IS_SMALLER_OR_EQUAL => Constraints\Comparison::LessThanOrEqualTo,
            );
        }
        
        if(isset(self::$TokenComparisons[$Token])) 
            return self::$TokenComparisons[$Token];
        else
            return null;
    }
    
    private function GetVariableValue(array $VariableTokens, array $VariableMap) {
        if(count($VariableTokens) === 1) {
            $VariableToken = $VariableTokens[0];
            if($VariableToken[0] === T_VARIABLE) {
                $VariableName = substr($VariableToken[1], 1);
                if(isset($VariableMap[$VariableName])) {
                    return $VariableMap[$VariableName];
                }
                else {
                    throw new \Exception;
                }
            }
            switch ($VariableToken[0]) {
                case T_CONSTANT_ENCAPSED_STRING:
                    return substr($VariableToken[1], 1, -1);
                case T_LNUMBER:
                    return (int)$VariableToken[1];
                case T_DNUMBER:
                    return (double)$VariableToken[1];
                case T_STRING:
                    switch (strtoupper($VariableToken[1])) {
                        case 'NULL':
                            return null;
                        case 'TRUE':
                            return true;
                        case 'FALSE':
                            return false;
                        default:
                            return constant($VariableToken[1]);
                    }
                    break;
                default:
                    throw new \Exception;
            }
        }
        else {
            if(!is_array($VariableTokens[0]))
                throw new \Exception;
            if(array_shift($VariableTokens)[0] !== T_NEW)
                throw new \Exception;
            
            $ObjectName = '';
            while(true) {
                $NameToken = array_shift($VariableTokens);
                if($NameToken === '(') {
                    array_unshift($VariableTokens, $NameToken);
                    break;
                }
                if(!is_array($NameToken))
                    throw new \Exception;
                if($NameToken[0] === T_NS_SEPARATOR)
                    $ObjectName .= '\\';
                else if($NameToken[0] === T_STRING)
                    $ObjectName .= $NameToken[1];
                
            }
            if(!class_exists($ObjectName))
                throw new \Exception;
            $ArgumentsArray = $this->GetFunctionArgumentTokensArray($VariableTokens);
            $ArgumentValues = array();
            foreach($ArgumentsArray as $ArgumentTokens) {
                 $ArgumentValues[] = $this->GetVariableValue($ArgumentTokens, $VariableMap);
            }
            return (new \ReflectionClass($ObjectName))->newInstanceArgs($ArgumentValues);
        }
    }
    
    private function GetFunctionArgumentTokensArray(array $ArgumentTokens) {
        array_shift($ArgumentTokens);
        array_pop($ArgumentTokens);
        if(count($ArgumentTokens) === 0)
            return array();
        
        $ArgumentTokensArray = array(array());
        $Count = 0;
        foreach($ArgumentTokens as $Token) {
            if($Token === ',') {
                $Count++;
                $ArgumentTokensArray[$Count] = array();
                continue;
            }
            $ArgumentTokensArray[$Count][] = $Token;
        }
        
        return $ArgumentTokensArray;
    }
    
    private function GetEntityPropertyFromTraverse(EntityMap $EntityMap, array $EntityTraverseTokens) {
        $EntityVariable = array_shift($EntityTraverseTokens);
        $EntityObjectOperator = array_shift($EntityTraverseTokens);
        
        return $this->GetEntityProperty($EntityMap, $EntityTraverseTokens);
    }
    
    private function GetEntityProperty(EntityMap $EntityMap, array $EntityPropertyTokens) {
        $MappedProperties = $EntityMap->GetProperties();
        
        if(count($EntityPropertyTokens) === 0)
            throw new \Exception;
                
        $TraversedPropertyTokensArray = array(array());
        $Count = 0;
        foreach($EntityPropertyTokens as $Key => $Token) {
            if(is_array($Token)) {
                if($Token[0] === T_OBJECT_OPERATOR) {
                    $Count++;
                    $TraversedPropertyTokensArray[$Count] = array();
                    continue;
                }
            }
            $TraversedPropertyTokensArray[$Count][] = $Token;
        }
        
        $TraversedGetters = array();
        foreach($TraversedPropertyTokensArray as $PropertyTokens) {
            $ObjectPropertyToken = array_shift($PropertyTokens);
            if(!is_array($ObjectPropertyToken))
                throw new \Exception;
            if($ObjectPropertyToken[0] !== T_STRING)
                throw new \Exception;

            $ObjectPropertyName = $ObjectPropertyToken[1];
            $NextToken = array_shift($PropertyTokens);
            $IsMethod = $NextToken === '(';
            
            if($IsMethod) {
                $TraversedGetters[] = new Properties\GetterMethod($ObjectPropertyName);
            }
            else {
                $TraversedGetters[] = new Properties\GetterField($ObjectPropertyName);
            }
        }
        
        $FinalGetter = array_shift($TraversedGetters);
        $IsTraversal = count($TraversedGetters) > 0;
        
        foreach($MappedProperties as $Property) {
            if(!$Property->CanGetValue()) {
                continue;
            }
            if($IsTraversal) {
                if($Property instanceof Properties\TraversedObjectProperty) {
                    $Count = 0;
                    foreach($Property->GetNestedGetterProperties() as $OtherNestedProperty) { 
                        $NestedGetter = $TraversedGetters[$Count];
                        $OtherNestedGetter = $OtherNestedProperty->GetPropertyGetter();
                        if(!$this->GettersMatch($NestedGetter, $OtherNestedGetter)) {
                            break;
                        }
                        $Count++;
                    }
                    if($this->GettersMatch($FinalGetter, $OtherNestedGetter->GetPropertyGetter())) {
                        return $Property;
                    }
                }
                continue;
            }
            else {
                if($Property instanceof Properties\GetterSetter) {
                    $OtherGetter = $Property->GetPropertyGetter();
                    if($this->GettersMatch($FinalGetter, $OtherGetter)) {
                        return $Property;
                    }
                }
            }
        }
    }
    
    private function GettersMatch($PropertyGetter, $OtherPropertyGetter) {
        if($PropertyGetter instanceof Properties\Method) {
            if($OtherPropertyGetter instanceof Properties\Method) {
                if($PropertyGetter->GetMethodName() === $OtherPropertyGetter->GetMethodName()) {
                    return true;
                }
            }
        }
        else if($PropertyGetter instanceof Properties\Field) {
            if($OtherPropertyGetter instanceof Properties\Field) {
                if($PropertyGetter->GetFieldName() === $OtherPropertyGetter->GetFieldName()) {
                    return true;
                }
            }
        }
        return false;
    }
}

?>