<?php

namespace Penumbra\Pinq;

interface IPredicated {
    
    /**
     * Specifies a predicate function.
     * 
     * Example predicate function:
     * <code>
     *  function (Car $Car) use ($Name) {
     *      return $Car->IsAvailable() && $Car->GetName() === $Name;
     *  }
     * </code>
     * 
     * @param callable $Function The predicate function
     * @return static 
     */
    public function Where(callable $Function);
}

?>
