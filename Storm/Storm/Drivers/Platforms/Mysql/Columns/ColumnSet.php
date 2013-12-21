<?php

namespace Storm\Drivers\Platforms\Mysql\Columns;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;

class ColumnSet implements IColumnSet {
    
    public function Guid($Name) {
        return new Column($Name, new DataTypes\HexedBinaryDataType(16));
    }
    
    public function IncrementInt32($Name) {
        return new Column($Name, new DataType('INT', [], new Columns\Traits\Increment()));
    }
    
    public function String($Name, $Length) {
        return new Column($Name, new DataType('VARCHAR', [$Length]));
    }
    
    public function Boolean($Name) {
        return new Column($Name, new DataTypes\BooleanBitDataType());
    }
    
    public function Byte($Name) {
        return new Column($Name, new DataTypes\IntDataType('TINYINT'));
    }
    
    public function Int16($Name) {
        return new Column($Name, new DataTypes\IntDataType('SMALLINT'));
    }
    
    public function Int32($Name) {
        return new Column($Name, new DataTypes\IntDataType('INT'));
    }
    
    public function Int64($Name) {
        return new Column($Name, new DataTypes\BigIntDataType(true));
    }
    
    public function UByte($Name) {
        return new Column($Name, new DataTypes\IntDataType('TINYINT', true));
    }
    
    public function UInt16($Name) {
        return new Column($Name, new DataTypes\IntDataType('SMALLINT', true));
    }
    
    public function UInt32($Name) {
        return new Column($Name, new DataTypes\IntDataType('INT', true));
    }
    
    public function UInt64($Name) {
        return new Column($Name, new DataTypes\BigIntDataType(true));
    }
    
    public function Double($Name) {
        return new Column($Name, new DataTypes\DoubleDataType());
    }
    
    public function Decimal($Name, $Length, $Precision) {
        return new Column($Name, new DataTypes\DecimalDataType($Name, $Length, $Precision));
    }
    
    public function Date($Name) {
        return new Column($Name, new DataTypes\DateDataType());
    }
    
    public function DateTime($Name) { 
        return new Column($Name, new DataTypes\DateTimeDataType());
    }
    
    public function Time($Name) {
        return new Column($Name, new DataTypes\TimeDataType());
    }
    
    public function Binary($Name, $Length) {
        return new Column($Name, new DataType('BINARY', [$Length]));
    }
    
    public function Blob($Name) {
        return new Column($Name, new DataType('BLOB'));
    }
}

?>