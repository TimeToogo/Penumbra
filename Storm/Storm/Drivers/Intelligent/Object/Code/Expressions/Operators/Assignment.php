<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions\Operators;

final class Assignment {
    const Equal = '=';
    const EqualReference = '=&';
    
    //Arthmetic
    const Addition = '+=';
    const Subtraction = '-=';
    const Multiplication = '*=';
    const Division = '/=';
    const Modulus = '%=';
    
    //Bitwise
    const BitwiseAnd = '&=';
    const BitwiseOr = '|=';
    const BitwiseXor = '^=';
    const ShiftLeft = '<<=';
    const ShiftRight = '>>=';
    
    //String
    const Concatenate = '.=';
}

?>