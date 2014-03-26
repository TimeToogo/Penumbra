<?php

namespace Penumbra\Drivers\Platforms\Mysql\Tables;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Queries;
use \Penumbra\Drivers\Platforms\Mysql\QueryBuilderExtensions;
use \Penumbra\Drivers\Base\Relational\Traits\IndexType;

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