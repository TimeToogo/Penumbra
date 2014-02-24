<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \Storm\Tests\Integration\ObjectRelationalModels\Blog\Entities\Blog;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class BlogRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Blog::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \Storm\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps\BlogMap */
        /* @var $Table \Storm\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables\Blogs */
        $Table = $Database->Blogs;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        $this->Map($EntityMap->Posts)->ToCollection($Table->Posts);
    }
}

?>