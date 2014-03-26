<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Mapping\Maps;

use \Penumbra\Drivers\Constant\Mapping;
use \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Post;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

final class PostRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Post::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps\PostMap */
        /* @var $Table \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables\Posts */
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
