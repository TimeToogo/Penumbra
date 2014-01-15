<?php

namespace StormTests\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormTests\One\Entities\Post;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class PostRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Post::GetType());
    }
    
    protected function PrimaryKeyTable(Relational\Database $Database) {
        return $Database->GetTable('Posts');
    }
    
    protected function InitializeMappings(Object\EntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormTests\One\Domain\Maps\PostMap */
        /* @var $Table \StormTests\One\Relational\Tables\Posts */
        $Table = $Database->Posts;
        
        $this->Map($EntityMap->Title)->ToColumn($Table->Title);
        $this->Map($EntityMap->Title)->ToColumn($Table->Title);
        $this->Map($EntityMap->Content)->ToColumn($Table->Content);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        //$this->Map($EntityMap->Blog)->ToEntity($Table->Blog);
        $this->Map($EntityMap->Tags)->ToCollection($Table->Tags);
    }
}

?>
