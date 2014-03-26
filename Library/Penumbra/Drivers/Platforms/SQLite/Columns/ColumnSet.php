<?php

namespace Penumbra\Drivers\Platforms\SQLite\Columns;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\Columns\Column;
use \Penumbra\Drivers\Base\Relational\Columns\DataType;

class ColumnSet implements IColumnSet {
    
    private function Unsupported($ColumnType) {
        return new \Penumbra\Drivers\Platforms\Base\UnsupportedColumnTypeException('SQLLite', $ColumnType);
    }
    
    public function Guid($Name, $PrimaryKey = true) {
        return new Column($Name, new DataTypes\HexedBinaryDataType(16), $PrimaryKey);
    }
    
    public function IncrementInt32($Name, $PrimaryKey = true) {
        return new Column($Name, new Columns\CastingDataType('INT', 'int'), $PrimaryKey, [new Columns\Traits\Increment()]);
    }
    
    public function IncrementInt64($Name, $PrimaryKey = true) {
        return new Column($Name, new Columns\CastingDataType('BIGINT', 'int'), $PrimaryKey, [new Columns\Traits\Increment()]);
    }
    
    public function String($Name, $Length, $PrimaryKey = false) {
        if($Length <= 8000) {
            return new Column($Name, new DataType('VARCHAR', [$Length]), $PrimaryKey);
        }
        else {
            return new Column($Name, new DataType('TEXT'), $PrimaryKey);
        }
    }
    
    public function Boolean($Name, $PrimaryKey = false) {
        return new Column($Name, new Columns\CastingDataType('TINYINT', 'bool'), $PrimaryKey);
    }
    
    public function Byte($Name, $PrimaryKey = false) {
        return new Column($Name, new Columns\CastingDataType('TINYINT', 'int'), $PrimaryKey);
    }
    
    public function Int16($Name, $PrimaryKey = false) {
        return new Column($Name, new Columns\CastingDataType('SMALLINT', 'int'), $PrimaryKey);
    }
    
    public function Int32($Name, $PrimaryKey = false) {
        return new Column($Name, new Columns\CastingDataType('INT', 'int'), $PrimaryKey);
    }
    
    public function Int64($Name, $PrimaryKey = false) {
        return new Column($Name, new Columns\CastingDataType('BIGINT', 'int'), $PrimaryKey);
    }
    
    public function UnsignedByte($Name, $PrimaryKey = false) {
        throw $this->Unsupported(__FUNCTION__);
    }
    
    public function UnsignedInt16($Name, $PrimaryKey = false) {
        throw $this->Unsupported(__FUNCTION__);
    }
    
    public function UnsignedInt32($Name, $PrimaryKey = false) {
        throw $this->Unsupported(__FUNCTION__);
    }
    
    public function UnsignedInt64($Name, $PrimaryKey = false) {
        throw $this->Unsupported(__FUNCTION__);
    }
    
    public function Double($Name, $PrimaryKey = false) {
        return new Column($Name, new Columns\CastingDataType('DOUBLE', 'double'), $PrimaryKey);
    }
    
    public function Decimal($Name, $Length, $Precision, $PrimaryKey = false) {
        return new Column($Name, new Columns\CastingDataType('DOUBLE', 'double', [$Length, $Precision]), $PrimaryKey);
    }
    
    public function Date($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\DateDataType(), $PrimaryKey);
    }
    
    public function DateTime($Name, $PrimaryKey = false) { 
        return new Column($Name, new DataTypes\DateTimeDataType(), $PrimaryKey);
    }
    
    public function Time($Name, $PrimaryKey = false) {
        throw $this->Unsupported(__FUNCTION__);
    }
    
    public function Binary($Name, $Length, $PrimaryKey = false) {
        return new Column($Name, new DataType('BLOB', [$Length]), $PrimaryKey);
    }
    
    public function Enum($Name, array $ValuesMap, $PrimaryKey = false) {
        return new Column($Name, new Columns\DataTypes\EnumDataType('ENUM', $ValuesMap, array_keys($ValuesMap)), $PrimaryKey);
    }

    public function ArrayOf(Column $DataTypeColumn) {
        
    }

    public function Interval($Name, $PrimaryKey = false) {
        throw $this->Unsupported(__FUNCTION__);
    }

}

?>