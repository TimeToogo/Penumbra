<?php

namespace StormExamples\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormExamples\One\Entities\Author;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class AuthorRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Author::GetType());
    }
    
    protected function PrimaryKeyTable(Relational\Database $Database) {
        return $Database->GetTable('Authors');
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormExamples\One\Domain\Maps\AuthorMap */
        /* @var $Table \StormExamples\One\Relational\Tables\Authors */
        $Table = $Database->Authors;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->FirstName)->ToColumn($Table->FirstName);
        $this->Map($EntityMap->LastName)->ToColumn($Table->LastName);
    }
}

?>