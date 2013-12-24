<?php

namespace StormTests\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormTests\One\Entities\Post;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class PostRelationalMap extends Mapping\EntityRelationalMap {
    public function __construct() {
        parent::__construct();
    }
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Post::GetType());
    }
    
    protected function InitializeMappings(Object\EntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormTests\One\Domain\Maps\PostMap */
        /* @var $Table \StormTests\One\Relational\Tables\Posts */
        $Table = $Database->Posts;
        
        $this->Map($EntityMap->BlogId)->ToColumn($Table->BlogId);
        $this->Map($EntityMap->Title)->ToColumn($Table->Title);
        $this->Map($EntityMap->Content)->ToColumn($Table->Content);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        $this->Map($EntityMap->Blog)->ToEntity(\StormTests\One\Entities\Blog::GetType(), $Table->Blog);
        $this->Map($EntityMap->Tags)->ToCollection(\StormTests\One\Entities\Tag::GetType(), $Table->Tags, Mapping\LoadingMode::ExtraLazy);
    }
}

?>
