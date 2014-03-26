<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Mapping\Maps;

use \Penumbra\Drivers\Constant\Mapping;
use \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Blog;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

final class BlogRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Blog::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps\BlogMap */
        /* @var $Table \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables\Blogs */
        $Table = $Database->Blogs;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        $this->Map($EntityMap->Posts)->ToCollection($Table->Posts);
    }
}

?>