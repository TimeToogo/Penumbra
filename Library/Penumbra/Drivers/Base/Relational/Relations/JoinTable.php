<?php

namespace Penumbra\Drivers\Base\Relational\Relations;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Penumbra\Drivers\Base\Relational\Traits\ForeignKey;
use \Penumbra\Drivers\Base\Relational\Traits\ForeignKeyMode;

abstract class JoinTable extends Base\Relational\Table {
    private $Table1;
    private $ForeignKey1;
    private $Table2;
    private $ForeignKey2;
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) { }
    
    protected function OnInitializeStructure(Relational\Database $Context) {
    }
    
    protected function OnInitializeRelatedStructure(Relational\Database $Context) {
        $this->Table1 = $this->Table1($Context);
        $this->Table2 = $this->Table2($Context);
        $this->ForeignKey1 = $this->MakeForeignKey($this->Table1, 1, $this->GetForeignKeyMap1($this->Table1));
        $this->ForeignKey2 = $this->MakeForeignKey($this->Table2, 2, $this->GetForeignKeyMap2($this->Table2));
    }
    
    /**
     * @return Relational\ITable
     */
    protected abstract function Table1(Relational\Database $Database);
    
    private function GetForeignKeyMap1(Relational\ITable $Table1) {
        $Map = new Map();
        $this->MapForeignKey1($Map, $Table1);
        return $Map;
    }
    protected abstract function MapForeignKey1(Map $Map, Relational\ITable $Table1);
    
    /**
     * @return Relational\ITable
     */
    protected abstract function Table2(Relational\Database $Database);
    
    private function GetForeignKeyMap2(Relational\ITable $Table2) {
        $Map = new Map();
        $this->MapForeignKey2($Map, $Table2);
        return $Map;
    }
    protected abstract function MapForeignKey2(Map $Map, Relational\ITable $Table2);
    
    /**
     * @return Relational\ITable
     */
    final public function GetTable1() {
        return $this->Table1;
    }
    /**
     * @return ForeignKey
     */
    final public function GetForeignKey1() {
        return $this->ForeignKey1;
    }
    
    /**
     * @return Relational\ITable
     */
    final public function GetTable2() {
        return $this->Table2;
    }
    /**
     * @return ForeignKey
     */
    final public function GetForeignKey2() {
        return $this->ForeignKey2;
    }
    
    private function MakeForeignKey(Relational\ITable $Table, $Number, Map $ReferencedColumnMap) {
        return new ForeignKey($this->GetName() . '_' . $Table->GetName() . $Number, $ReferencedColumnMap,
                ForeignKeyMode::Cascade, ForeignKeyMode::Cascade);
    }
    
    /**
     * @return ForeignKey
     * @throws \Penumbra\Core\UnexpectedValueException
     */
    final public function GetForeignKey(Relational\ITable $Table) {
        if($this->Table1->Is($Table)) {
            return $this->GetForeignKey1();
        }
        else if($this->Table2->Is($Table)) {
            return $this->GetForeignKey2();
        }
        else {
            throw new \Penumbra\Core\UnexpectedValueException(
                    'The supplied table must one of %s, %s: %s given',
                    $this->Table1->GetName(),
                    $this->Table2->GetName(),
                    $Table->GetName());
        }
    }
    
    final protected function RegisterToManyRelations(Registrar $Registrar, Relational\Database $Context) { }
    
    final protected function RegisterToOneRelations(Registrar $Registrar, Relational\Database $Context) {
        $Registrar->Register(new ToOneRelation($this->ForeignKey1));
        
        $Registrar->Register(new ToOneRelation($this->ForeignKey2));
    }
    
    protected function RegisterStructuralTraits(Registrar $Registrar) {
        
    }
    
    protected function RegisterRelationalTraits(Registrar $Registrar, Relational\Database $Context) {
        $Registrar->Register($this->ForeignKey1);
        $Registrar->Register($this->ForeignKey2);
    }
    
    /**
     * @param Relational\ColumnData $PrimaryKey1
     * @param Relational\ColumnData $PrimaryKey2
     * @return Relational\Row
     */
    final public function JoinRow(Relational\ColumnData $PrimaryKey1, Relational\ColumnData $PrimaryKey2) {
        $Row = $this->Row();
        
        $this->ForeignKey1->MapReferencedToParentKey($PrimaryKey1, $Row);
        $this->ForeignKey2->MapReferencedToParentKey($PrimaryKey2, $Row);
        
        return $Row;
    }
}

?>