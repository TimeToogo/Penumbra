<?php

namespace StormTests\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormTests\One\Entities\Tag;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class TagRelationalMap extends Mapping\EntityRelationalMap {
    public function __construct() {
        parent::__construct();
    }
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Tag::GetType());
    }
    
    protected function InitializeMappings(Object\EntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormTests\One\Domain\Maps\TagMap */
        /* @var $Table \StormTests\One\Relational\Tables\Tags */
        $Table = $Database->Tags;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
    }
}

?>
