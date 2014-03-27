<?php

namespace Penumbra\Pinq;

interface IOrdered {
    
    /**
     * Specifies the function to use as an ascending ordering for the criteria.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     * 
     * @param callable $Function The expression closure
     * @return static
     */
    public function OrderBy(callable $Function);
    
    /**
     * Specifies the function to use as an descending ordering for the criteria.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     * 
     * @param callable $Function The expression closure
     * @return static
     */
    public function OrderByDescending(callable $Function);
}

?>
