<?php

namespace Storm\Pinq;

use \Storm\Core\Object;
use \Storm\Pinq\Functional;

class PinqException extends Object\ObjectException {
    public static function ContainsUnresolvableVariables(\ReflectionFunctionAbstract $Reflection, array $UnresolvableVariables) {
        return self::InvalidFunctionMessage('contains unresolvable variables: $%s',
                $Reflection,
                implode(', $', $UnresolvableVariables));
    }
    
    public static function MustContainValidReturnExpression($Type) {
        return new self("Cannot use function as $Type: Must contain a valid return statement");
    }
    
    public static function UnresolvableAggregateTraversal() {
        return new self('Cannot resolve aggregate traversal');
    }
    
    public static function InvalidFunctionSignature(\ReflectionFunctionAbstract $Reflection, array $ParameterTypeHints = []) {
        return self::InvalidFunctionMessage('function has an invalid signature, expecting %s parameter(s) with types %s, %d given with types %s',
                $Reflection,
                count($ParameterTypeHints),
                implode(', ', $ParameterTypeHints),
                $Reflection->getNumberOfParameters(),
                implode(', ', array_map(function($I) { return $I->getClass() ? $I->getClass()->name : '{NONE}'; }, $Reflection->getParameters())));
    }
    
    public static function InvalidFunctionMessage($MessageFormat, \ReflectionFunctionAbstract $Reflection) {
        return self::Construct(array_merge([
            'Invalid function defined in %s lines %d-%d: ' . $MessageFormat,
            $Reflection->getFileName(),
            $Reflection->getStartLine(),
            $Reflection->getEndLine()],
            array_slice(func_get_args(), 2)));
    }
}

?>
