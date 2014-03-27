<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\Columns\Column;
use \Penumbra\Drivers\Base\Relational\Columns\DataType;

class ColumnSet implements IColumnSet {
    
    public function Guid($Name, $PrimaryKey = true) {
        return new Column($Name, new DataTypes\BinaryGuidDataType(), $PrimaryKey);
    }
    
    public function IncrementInt32($Name, $PrimaryKey = true) {
        return new Column($Name, new DataTypes\IntDataType('INT'), $PrimaryKey, [new Columns\Traits\Increment()]);
    }
    
    public function IncrementInt64($Name, $PrimaryKey = true) {
        return new Column($Name, new DataTypes\IntDataType('BIGINT'), $PrimaryKey, [new Columns\Traits\Increment()]);
    }
    
    public function String($Name, $Length, $PrimaryKey = false) {
        if($Length <= 8000) {
            return new Column($Name, new DataType('VARCHAR', [$Length]), $PrimaryKey);
        }
        else if($Length <= 65535) {
            return new Column($Name, new DataType('TEXT'), $PrimaryKey);
        }
        else if($Length <= 16777215) {
            return new Column($Name, new DataType('MEDIUMTEXT'), $PrimaryKey);
        }
        else if($Length <= 4294967295) {
            return new Column($Name, new DataType('LONGTEXT'), $PrimaryKey);
        }
        else {
            throw new \Penumbra\Drivers\Base\Relational\PlatformException(
                    'Exceeded maximum string 4294967295 length Mysql TEXT datatypes: $d given',
                    $Length);
        }
    }
    
    public function Boolean($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\BooleanBitDataType(), $PrimaryKey);
    }
    
    public function Byte($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\IntDataType('TINYINT'), $PrimaryKey);
    }
    
    public function Int16($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\IntDataType('SMALLINT'), $PrimaryKey);
    }
    
    public function Int32($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\IntDataType('INT'), $PrimaryKey);
    }
    
    public function Int64($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\BigIntDataType(true), $PrimaryKey);
    }
    
    public function UnsignedByte($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\IntDataType('TINYINT', true), $PrimaryKey);
    }
    
    public function UnsignedInt16($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\IntDataType('SMALLINT', true), $PrimaryKey);
    }
    
    public function UnsignedInt32($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\IntDataType('INT', true), $PrimaryKey);
    }
    
    public function UnsignedInt64($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\BigIntDataType(true), $PrimaryKey);
    }
    
    public function Double($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\DoubleDataType(), $PrimaryKey);
    }
    
    public function Decimal($Name, $Length, $Precision, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\DecimalDataType($Name, $Length, $Precision), $PrimaryKey);
    }
    
    public function Date($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\DateDataType(), $PrimaryKey);
    }
    
    public function DateTime($Name, $PrimaryKey = false) { 
        return new Column($Name, new DataTypes\DateTimeDataType(), $PrimaryKey);
    }
    
    public function Time($Name, $PrimaryKey = false) {
        return new Column($Name, new DataTypes\TimeDataType(), $PrimaryKey);
    }
    
    public function Binary($Name, $Length, $PrimaryKey = false) {
        if($Length <= 8000) {
            return new Column($Name, new DataType('VARBINARY', [$Length]), $PrimaryKey);
        }
        else if($Length <= 65535) {
            return new Column($Name, new DataType('BLOB'), $PrimaryKey);
        }
        else if($Length <= 16777215) {
            return new Column($Name, new DataType('MEDIUMBLOB'), $PrimaryKey);
        }
        else if($Length <= 4294967295) {
            return new Column($Name, new DataType('LONGBLOB'), $PrimaryKey);
        }
        else {
            throw new \Penumbra\Drivers\Base\Relational\PlatformException(
                    'Exceeded maximum binary 4294967295 length Mysql TEXT datatypes: $d given',
                    $Length);
        }
    }

    public function Interval($Name, $PrimaryKey = false) {
        throw new \Penumbra\Drivers\Base\Relational\PlatformException('Mysql does not support an interval datatype');
    }
    
    public function Enum($Name, array $ValuesMap, $PrimaryKey = false) {
        return new Column($Name, new Columns\DataTypes\EnumDataType('ENUM', $ValuesMap, array_keys($ValuesMap)), $PrimaryKey);
    }
    
    public function ArrayOf(Column $DataTypeColumn) {
        throw new \Penumbra\Drivers\Base\Relational\PlatformException('Mysql does not support an array datatype');
    }
}

?>