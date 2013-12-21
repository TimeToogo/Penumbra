<?php

namespace Storm\Drivers\Platforms\Mysql\Tables;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Platforms\Mysql\QueryBuilderExtensions;
use \Storm\Drivers\Base\Relational\Traits\IndexType;

final class IndexStorageType {
    const BTree = 0;
    const RTree = 1;
    const Hash = 2;
}

class Index extends Relational\Traits\Index {
    
    private $StorageType = null;
    
    public function __construct($Name, array $Columns, $Type = IndexType::Plain, $StorageType = IndexStorageType::BTree) {
        parent::__construct($Name, $Columns, $Type);
        
        if($Type !== IndexType::FullText)
            $this->StorageType = $StorageType;
    }
    
    final public function GetStorageType() {
        return $this->StorageType;
    }
    
    public function GetColumnDirection(Relational\Columns\Column $Column) {
        return Relational\Traits\IndexDirection::Ascending;
    }
    
    protected function IsStructuralTrait(Relational\StructuralTableTrait $OtherTrait) {
        if($this->StorageType !== $OtherTrait->StorageType)
            return false;
        else
            return parent::IsTrait($OtherTrait);
    }
    
}

?>