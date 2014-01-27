<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullPlatform implements Relational\IPlatform {
    public function __sleep() {
        
    }
    
    public function Commit(array $TablesOrderedByPersistingDependency, array $TablesOrderedByDiscardingDependency, \Storm\Core\Relational\Transaction $Transaction) {
        
    }

    public function GetColumnSet() {
        return new NullColumnSet();
    }

    public function GetConnection() {
        
    }
    
    public function SetConnection(Relational\Queries\IConnection $Connection) {
        
    }
    
    public function GetCriterionCompiler() {
        
    }

    public function GetDatabaseSyncer() {
        
    }

    public function GetExpressionCompiler() {
        
    }

    public function GetExpressionMapper() {
        
    }

    public function GetIdentifierEscaper() {
        
    }

    public function GetKeyGeneratorSet() {
        return new NullKeyGeneratorSet();
    }

    public function GetQueryExecutor() {
        
    }

    public function Select(\Storm\Core\Relational\Request $Request) {
        
    }

    public function Sync(Relational\Database $Database) {
        
    }

}

final class NullKeyGeneratorSet implements Relational\PrimaryKeys\IKeyGeneratorSet {
    public function Guid() {
        
    }

    public function Increment() {
        
    }

    public function Sequence($Name) {
        
    }

}

final class NullColumnSet implements Relational\Columns\IColumnSet {
    public function Binary($Name, $Length, $PrimaryKey = false) {
        
    }

    public function Boolean($Name, $PrimaryKey = false) {
        
    }

    public function Byte($Name, $PrimaryKey = false) {
        
    }

    public function Date($Name, $PrimaryKey = false) {
        
    }

    public function DateTime($Name, $PrimaryKey = false) {
        
    }

    public function Decimal($Name, $Length, $Precision, $PrimaryKey = false) {
        
    }

    public function Double($Name, $PrimaryKey = false) {
        
    }

    public function Enum($Name, array $ValuesMap, $PrimaryKey = false) {
        
    }

    public function Guid($Name, $PrimaryKey = true) {
        
    }

    public function IncrementInt32($Name, $PrimaryKey = true) {
        
    }

    public function IncrementInt64($Name, $PrimaryKey = true) {
        
    }

    public function Int16($Name, $PrimaryKey = false) {
        
    }

    public function Int32($Name, $PrimaryKey = false) {
        
    }

    public function Int64($Name, $PrimaryKey = false) {
        
    }

    public function String($Name, $Length, $PrimaryKey = false) {
        
    }

    public function Time($Name, $PrimaryKey = false) {
        
    }

    public function UnsignedByte($Name, $PrimaryKey = false) {
        
    }

    public function UnsignedInt16($Name, $PrimaryKey = false) {
        
    }

    public function UnsignedInt32($Name, $PrimaryKey = false) {
        
    }

    public function UnsignedInt64($Name, $PrimaryKey = false) {
        
    }

}

?>