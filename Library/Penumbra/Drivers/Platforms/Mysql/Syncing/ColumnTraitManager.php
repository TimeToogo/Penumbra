<?php

namespace Penumbra\Drivers\Platforms\Mysql\Syncing;

use \Penumbra\Drivers\Base\Relational\Syncing;
use \Penumbra\Drivers\Base\Relational\Table;
use \Penumbra\Drivers\Base\Relational\Columns\Traits;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Platforms\Mysql;

class ColumnTraitManager extends Syncing\Traits\ColumnTraitManager {
    final protected function Initialize() {
        $this->Register(Mysql\Columns\CharacterSet::GetType(), 
            [$this, 'AppendCharacterSet']);
        
        $this->Register(Mysql\Columns\Collation::GetType(), 
            [$this, 'AppendCollation']);
        
        $this->Register(Traits\Comment::GetType(), 
            [$this, 'AppendComment']);
        
        $this->Register(Traits\DefaultValue::GetType(), 
            [$this, 'AppendDefaultValue']);
        
        $this->Register(Traits\Increment::GetType(), 
            [$this, 'AppendIncrement']);
        
        $this->Register(Traits\NotNullable::GetType(), 
            [$this, 'AppendNotNullable']);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Appending Methods">
    public function AppendCharacterSet(QueryBuilder $QueryBuilder, Mysql\Columns\CharacterSet $Trait) {
        $QueryBuilder->AppendIdentifier('CHARACTER SET #', [$Trait->GetName()]);
    }


    public function AppendCollation(QueryBuilder $QueryBuilder, Mysql\Columns\Collation $Trait) {
        $QueryBuilder->AppendIdentifier('COLLATE #', [$Trait->GetName()]);
    }


    public function AppendComment(QueryBuilder $QueryBuilder, Traits\Comment $Trait) {
        $QueryBuilder->AppendEscaped('COMMENT #', $Trait->GetValue());
    }


    public function AppendDefaultValue(QueryBuilder $QueryBuilder, Traits\DefaultValue $Trait) {
        $QueryBuilder->AppendEscaped('DEFAULT #', $Trait->GetValue());
    }


    public function AppendIncrement(QueryBuilder $QueryBuilder, Traits\Increment $Trait) {
        $QueryBuilder->Append('AUTO_INCREMENT');
    }


    public function AppendNotNullable(QueryBuilder $QueryBuilder, Traits\NotNullable $Trait) {
        $QueryBuilder->Append('NOT NULL');
    }

    // </editor-fold>
}

?>