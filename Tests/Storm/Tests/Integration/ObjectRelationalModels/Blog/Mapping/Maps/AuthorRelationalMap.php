<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \Storm\Tests\Integration\ObjectRelationalModels\Blog\Entities\Author;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class AuthorRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Author::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \Storm\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps\AuthorMap */
        /* @var $Table \Storm\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables\Authors */
        $Table = $Database->Authors;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->FirstName)->ToColumn($Table->FirstName);
        $this->Map($EntityMap->LastName)->ToColumn($Table->LastName);
    }
}

?>