<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Relational;
use \Storm\Drivers\Base;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Traits\ForeignKeyMode;

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
        $this->ForeignKey1 = $this->MakeForeignKey($this->Table1, $this->GetForeignKeyMap1($this->Table1));
        $this->ForeignKey2 = $this->MakeForeignKey($this->Table2, $this->GetForeignKeyMap2($this->Table2));
    }
    
    /**
     * @return Relational\Table
     */
    protected abstract function Table1(Relational\Database $Database);
    
    private function GetForeignKeyMap1(Relational\Table $Table1) {
        $Map = new Map();
        $this->MapForeignKey1($Map, $Table1);
        return $Map;
    }
    protected abstract function MapForeignKey1(Map $Map, Relational\Table $Table1);
    
    /**
     * @return Relational\Table
     */
    protected abstract function Table2(Relational\Database $Database);
    
    private function GetForeignKeyMap2(Relational\Table $Table2) {
        $Map = new Map();
        $this->MapForeignKey2($Map, $Table2);
        return $Map;
    }
    protected abstract function MapForeignKey2(Map $Map, Relational\Table $Table2);
    
    /**
     * @return Relational\Table
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
     * @return Relational\Table
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
    
    private function MakeForeignKey(Relational\Table $Table, Map $ReferencedColumnMap) {
        return new ForeignKey($this->GetName() . '_' . $Table->GetName(), $ReferencedColumnMap,
                ForeignKeyMode::Cascade, ForeignKeyMode::Cascade);
    }
    
    /**
     * @return ForeignKey
     * @throws Exception
     */
    final public function GetForeignKey(Relational\Table $Table) {
        if($this->GetTable1()->Is($Table)) {
            return $this->GetForeignKey1();
        }
        else if($this->GetTable2()->Is($Table)) {
            return $this->GetForeignKey2();
        }
        else {
            //TODO: error
            throw new \Exception;
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
     * @param array $PrimaryKey1Data
     * @param array $PrimaryKey2Data
     * @return Relational\Row
     */
    final public function JoinRow(array &$PrimaryKey1Data, array &$PrimaryKey2Data) {
        $JoinRow = array();
        
        $this->ForeignKey1->MapReferencedToParentKey($PrimaryKey1Data, $JoinRow);
        $this->ForeignKey2->MapReferencedToParentKey($PrimaryKey2Data, $JoinRow);
        
        return $JoinRow;
    }
}

?>