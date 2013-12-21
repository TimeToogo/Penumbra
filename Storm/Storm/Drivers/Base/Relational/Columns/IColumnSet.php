<?php

namespace Storm\Drivers\Base\Relational\Columns;

interface IColumnSet {
    
    /**
     * @return Column
     */
    public function Guid($Name);
    
    /**
     * @return Column
     */
    public function IncrementInt32($Name);
    
    /**
     * @return Column
     */
    public function String($Name, $Length);
    
    /**
     * @return Column
     */
    public function Boolean($Name);
    
    /**
     * @return Column
     */
    public function Byte($Name);
    
    /**
     * @return Column
     */
    public function Int16($Name);
    
    /**
     * @return Column
     */
    public function Int32($Name);
    
    /**
     * @return Column
     */
    public function Int64($Name);
    
    /**
     * @return Column
     */
    public function UByte($Name);
    
    /**
     * @return Column
     */
    public function UInt16($Name);
    
    /**
     * @return Column
     */
    public function UInt32($Name);
    
    /**
     * @return Column
     */
    public function UInt64($Name);
    
    /**
     * @return Column
     */
    public function Double($Name);
    
    /**
     * @return Column
     */
    public function Decimal($Name, $Length, $Precision);
    
    /**
     * @return Column
     */
    public function Date($Name);
    
    /**
     * @return Column
     */
    public function DateTime($Name);
    
    /**
     * @return Column
     */
    public function Time($Name);
    
    /**
     * @return Column
     */
    public function Binary($Name, $Length);
    
    /**
     * @return Column
     */
    public function Blob($Name);
}

?>