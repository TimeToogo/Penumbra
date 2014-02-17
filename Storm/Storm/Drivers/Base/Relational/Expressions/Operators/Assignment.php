<?php

 namespace Storm\Drivers\Base\Relational\Expressions\Operators;

final class Assignment {
    const Equal = 0;
    
    //Arthmetic
    const Addition = 100;
    const Subtraction = 101;
    const Multiplication = 103;
    const Division = 104;
    const Modulus = 105;
    const Power = 106;
        
    //Bitwise
    const BitwiseAnd = 200;
    const BitwiseOr = 201;
    const BitwiseXor = 202;
    const ShiftLeft = 203;
    const ShiftRight = 204;
    
    //String
    const Concatenate = 300;
}

?>