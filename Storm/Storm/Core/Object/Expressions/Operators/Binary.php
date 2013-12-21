<?php

namespace Storm\Core\Object\Expressions\Operators;

final class Binary {
    //Arthmetic
    const Addition = '+';
    const Subtraction = '-';
    const Multiplication = '*';
    const Division = '/';
    const Modulus = '%';
    
    //Bitwise
    const BitwiseAnd = '&';
    const BitwiseOr = '|';
    const BitwiseXor = '^';
    const ShiftLeft = '<<';
    const ShiftRight = '>>';
    
    //Logical
    const LogicalAnd = '&&';
    const LogicalOr = '||';
    const Equality = '==';
    const Identity = '===';
    const Inequality = '!=';
    const NonIdentity = '!==';
    const LessThan = '<';
    const LessThanOrEqualTo = '<=';
    const GreaterThan = '>';
    const GreaterThanOrEqualTo = '>=';
    
    //String
    const Concatenation = '.';
    
    //Type
    const IsInstanceOf = 'instanceof';
}

?>