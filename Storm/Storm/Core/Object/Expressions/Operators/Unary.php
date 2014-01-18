<?php

namespace Storm\Core\Object\Expressions\Operators;

/**
 * The class containing php level unary operators
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
}

?>