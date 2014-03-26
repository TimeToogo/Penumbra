<?php

namespace PenumbraExamples\One\Mapping\Maps;

use \Penumbra\Drivers\Constant\Mapping;
use \PenumbraExamples\One\Entities\Tag;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

final class TagRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Tag::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \PenumbraExamples\One\Domain\Maps\TagMap */
        /* @var $Table \PenumbraExamples\One\Relational\Tables\Tags */
        $Table = $Database->Tags;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->Number)->ToColumn($Table->Number);
    }
}

?>
