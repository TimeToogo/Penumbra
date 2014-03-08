<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Functional;

class FluentException extends Object\ObjectException {
    public static function ContainsUnresolvableVariables(\ReflectionFunctionAbstract $Reflection, array $UnresolvableVariables) {
        return new self('Function defined in %s lines %d-%d: contains unresolvable variables: $%s',
                $Reflection->getFileName(),
                $Reflection->getStartLine(),
                $Reflection->getEndLine(),
                implode(', $', $UnresolvableVariables));
    }
    
    public static function MustContainValidReturnExpression($Type) {
        return new self("Cannot use expression tree as $Type: Must contain a valid return statement");
    }
}

?>
