<?php

namespace Storm\Drivers\Base\Relational\Columns;

interface IColumnSet {
    
    /**
     * @return Column
     */
    public function Guid($Name, $PrimaryKey = true);
    
    /**
     * @return Column
     */
    public function IncrementInt32($Name, $PrimaryKey = true);
    
    /**
     * @return Column
     */
    public function IncrementInt64($Name, $PrimaryKey = true);
    
    /**
     * @return Column
     */
    public function String($Name, $Length, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Boolean($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Byte($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Int16($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Int32($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Int64($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function UnsignedByte($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function UnsignedInt16($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function UnsignedInt32($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function UnsignedInt64($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Double($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Decimal($Name, $Length, $Precision, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Date($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function DateTime($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Time($Name, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Binary($Name, $Length, $PrimaryKey = false);
    
    /**
     * @return Column
     */
    public function Enum($Name, array $ValuesMap, $PrimaryKey = false);    
}

?>