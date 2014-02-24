<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \Storm\Tests\Integration\ObjectRelationalModels\Blog\Entities\Post;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class PostRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Post::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \Storm\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps\PostMap */
        /* @var $Table \Storm\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables\Posts */
        $Table = $Database->Posts;
        
        $this->Map($EntityMap->Title)->ToColumn($Table->Title);
        $this->Map($EntityMap->Content)->ToColumn($Table->Content);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        //$this->Map($EntityMap->Blog)->ToEntity($Table->Blog);
        $this->Map($EntityMap->Author)->ToEntity($Table->Author);
        $this->Map($EntityMap->Tags)->ToCollection($Table->Tags);
    }
}

?>
