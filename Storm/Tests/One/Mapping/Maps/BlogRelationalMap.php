<?php

namespace StormTests\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormTests\One\Entities\Blog;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class BlogRelationalMap extends Mapping\EntityRelationalMap {
    public function __construct() {
        parent::__construct();
    }
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Blog::GetType());
    }
    
    protected function InitializeMappings(Object\EntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormTests\One\Domain\Maps\BlogMap */
        /* @var $Table \StormTests\One\Relational\Tables\Blogs */
        $Table = $Database->Blogs;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        $this->Map($EntityMap->Posts)->ToCollection(\StormTests\One\Entities\Post::GetType(), $Table->Posts);
    }
}

?>