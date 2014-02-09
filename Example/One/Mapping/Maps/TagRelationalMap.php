<?php

namespace StormExamples\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormExamples\One\Entities\Tag;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class TagRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Tag::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormExamples\One\Domain\Maps\TagMap */
        /* @var $Table \StormExamples\One\Relational\Tables\Tags */
        $Table = $Database->Tags;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->Number)->ToColumn($Table->Number);
    }
}

?>
