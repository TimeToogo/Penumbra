<?php

namespace Storm\Pinq;

interface IAggregate {
    const IAggregateType = __CLASS__;

    /**
     * Returns the first value
     * 
     * @return mixed The first value 
     */
    public function First();
    
    /**
     * Returns the amount of the values.
     * 
     * @return int
     */
    public function Count();    
    
    /**
     * Returns the maximum value.
     * 
     * @param callable $Function The function which will return the values
     * @return int|null
     */
    public function Maximum(callable $Function);
    
    
    /**
     * Returns the maximum value.
     * 
     * @param callable $Function The function which will return the values
     * @return int|null
     */
    public function Minimum(callable $Function);
    
    /**
     * Returns the sum of the values.
     * 
     * @param callable $Function The function which will return the values
     * @return int|null
     */
    public function Sum(callable $Function);
    
    
    /**
     * Returns the average of the values.
     * 
     * @param callable $Function The function which will return the values
     * @return double|null
     */
    public function Average(callable $Function);
    
    
    /**
     * Returns a boolean of if all the values evaluate to true
     * 
     * @param callable $Function The function which will return the values
     * @return boolean|null
     */
    public function All(callable $Function);
    
    /**
     * Returns a boolean of if any the values evaluate to true
     * 
     * @param callable $Function The function which will return the values
     * @return boolean|null
     */
    public function Any(callable $Function);
    
    /**
     * Returns a string of all the values concatented with the separator
     * 
     * @param string $Delimiter The string to delimit the values by
     * @param callable $Function The function which will return the values
     * @return boolean|null
     */
    public function Implode($Delimiter, callable $Function);
}

?>