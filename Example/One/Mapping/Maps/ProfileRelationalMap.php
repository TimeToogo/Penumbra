<?php

namespace PenumbraExamples\One\Mapping\Maps;

use \Penumbra\Drivers\Constant\Mapping;
use \PenumbraExamples\One\Entities\Profile;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

final class ProfileRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Profile::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \PenumbraExamples\One\Domain\Maps\ProfileMap */
        /* @var $Table \PenumbraExamples\One\Relational\Tables\Profiles */
        $Table = $Database->Profiles;
        
        $this->Map($EntityMap->Author)->ToEntity($Table->Author);
        $this->Map($EntityMap->DateOfBirth)->ToColumn($Table->DateOfBirth);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->Location)->ToColumn($Table->Location);
    }
}

?>