<?php

namespace PenumbraExamples\One\Mapping\Maps;

use \Penumbra\Drivers\Constant\Mapping;
use \PenumbraExamples\One\Entities\Post;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

final class PostRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Post::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \PenumbraExamples\One\Domain\Maps\PostMap */
        /* @var $Table \PenumbraExamples\One\Relational\Tables\Posts */
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
