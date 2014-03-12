<?php

namespace Storm\Pinq;

interface IGroupable {
    
    /**
     * Specifies the grouping function
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetBrand();
     * }
     * </code>
     * 
     * @param callable $Function The grouping function
     * @return static
     */
    public function GroupBy(callable $Function);
        
    /**
     * Specifies the aggregate predicate function.
     * 
     * Example aggregate predicate closure:
     * <code>
     * function (IAggregate $Cars) {
     *     return $Cars->Maximum(function (Car $Car) { return $Car->GetPrice(); }) < 50000;
     * }
     * </code>
     * 
     * @param callable $Function The aggregate predicate function
     * @return static
     */
    public function Having(callable $Function);
    
}

?>
