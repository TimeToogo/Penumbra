<?php

namespace PenumbraExamples\One\Mapping\Maps;

use \Penumbra\Drivers\Constant\Mapping;
use \PenumbraExamples\One\Entities\Author;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

final class AuthorRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Author::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \PenumbraExamples\One\Domain\Maps\AuthorMap */
        /* @var $Table \PenumbraExamples\One\Relational\Tables\Authors */
        $Table = $Database->Authors;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->FirstName)->ToColumn($Table->FirstName);
        $this->Map($EntityMap->LastName)->ToColumn($Table->LastName);
        
        $this->Map($EntityMap->Profile)->ToEntity($Table->Profile);
        $this->Map($EntityMap->Friends)->ToCollection($Table->Friends);
    }
}

?>