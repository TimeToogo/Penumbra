<?php

namespace Storm\Drivers\Pinq\Object;

interface IAggregate {        
    /**
     * Returns the amount of the values.
     * 
     * @return void
     */
    public function Count();
    
    /**
     * Returns the sum of the unique values.
     * 
     * @return void
     */
    public function CountDistinct();
    
    /**
     * Alias of Maximum.
     * 
     * @return void
     */
    public function Max();
    
    /**
     * Returns the maximum value.
     * 
     * @return void
     */
    public function Maximum();
    
    /**
     * Alias of Minimum.
     * 
     * @return void
     */
    public function Min();
    
    /**
     * Returns the maximum value.
     * 
     * @return void
     */
    public function Minimum();
    
    /**
     * Returns the sum of the values.
     * 
     * @param callable $ValueFunction The function which will return the values to sum
     * @return void
     */
    public function Sum();
    
    /**
     * Returns the sum of unique value.
     * 
     * @return void
     */
    public function SumDistinct();
    
    /**
     * Returns the average of the values.
     * 
     * @return void
     */
    public function Average();
    
    /**
     * Returns the average of the unique values.
     * 
     * @return void
     */
    public function AverageDistinct();
    
    /**
     * Returns a boolean of if all the values evaluate to true
     * 
     * @return void
     */
    public function All();
    
    /**
     * Returns a boolean of if any the values evaluate to true
     * 
     * @return void
     */
    public function Any();
    
    /**
     * Returns a string of all the values concatented with the separator
     * 
     * @param string $Delimiter The string to delimit the values by
     * @return void
     */
    public function Implode($Delimiter);
    
    /**
     * Returns a string of all the unique values concatented with the separator
     * 
     * @param string $Delimiter The string to delimit the values by
     * @return void
     */
    public function ImplodeDistinct($Delimiter);
}

?>