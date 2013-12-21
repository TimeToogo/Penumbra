<?php

 namespace Storm\Drivers\Base\Relational\Expressions\Operators;

final class Unary {
    //Arithmetic
    const Negation = 0;
    const Increment = 1;
    const Decrement = 2;
    const PreIncrement = 3;
    const PreDecrement = 4;
    
    //Bitwise
    const BitwiseNot = 100;
    
    //Logical
    const Not = 200;
}

?>