<?php

namespace StormExamples\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormExamples\One\Entities\Post;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class PostRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Post::GetType());
    }
    
    protected function PrimaryKeyTable(Relational\Database $Database) {
        return $Database->GetTable('Posts');
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormExamples\One\Domain\Maps\PostMap */
        /* @var $Table \StormExamples\One\Relational\Tables\Posts */
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
