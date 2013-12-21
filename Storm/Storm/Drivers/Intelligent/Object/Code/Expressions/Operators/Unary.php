<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions\Operators;

final class Unary {
    //Arithmetic
    const Negation = '-%s';
    const Increment = '++%s';
    const Decrement = '--%s';
    const PreIncrement = '%s++';
    const PreDecrement = '%s--';
    
    //Bitwise
    const BitwiseNot = '~%s';
    
    //Logical
    const Not = '!%s';
    
    //Error Control
    const ShutUp = '@%s';
}

?>