<?php

namespace Storm\Pinq;

interface IQueryable extends IAggregate, IGroupable, IPredicated, IOrdered, IRanged, \IteratorAggregate {
    
    /**
     * Specifies the function return the data.
     * 
     * You can take the aggregate for aggregate functionality:
     * <code>
     *  function (IAggregate $Cars) {
     *      return [
     *          'Brand' => $Cars->First()->GetBrand(),
     *          'MostExpensive' => $Cars->Maximum(function (Car $Car) { return $Car->GetPrice(); }),
     *          'AmountOfCars' => $Cars->Count(),
     *          'CarNames' => $Cars->Implode(', ', function (Car $Car) { return $Car->GetName(); }),
     *      ];
     *  }
     * </code>
     * 
     * Or the entity for for the simple property retrieval:
     * <code>
     *  function (Car $Car) {
     *      return [
     *          'Brand' => $Car->GetBrand(),
     *          'Model Number' => $Car->GetModelNumber(),
     *          'Sale Price' => $Car->GetRetailPrice() - $Car->GetDiscountPrice(),
     *      ];
     *  }
     * </code>
     * 
     * Or both for full type hinting:
     * <code>
     *  function (Car $Car, IAggregate $Aggregate) {
     *      return [
     *          'Brand' => $Car->GetBrand(),
     *          'Sale Price' => $Car->GetRetailPrice() - $Car->GetDiscountPrice(),
     *          'Cheapest Car' => $Aggregate->Minimum(function (Car $Car) { return $Car->GetPrice(); }),
     *      ];
     *  }
     * </code>
     * 
     * @param callable $Function The function returning the data to select
     * @return array The selected values 
     */
    public function Select(callable $Function);
    
    
    /**
     * Returns the array of values
     * 
     * @return array The array of values
     */
    public function AsArray();
    
    /**
     * Returns whether any values exist.
     * 
     * @return boolean
     */
    public function Exists();
}

?>
