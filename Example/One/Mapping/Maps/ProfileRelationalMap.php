<?php

namespace StormExamples\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormExamples\One\Entities\Profile;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class ProfileRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Profile::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormExamples\One\Domain\Maps\ProfileMap */
        /* @var $Table \StormExamples\One\Relational\Tables\Profiles */
        $Table = $Database->Profiles;
        
        $this->Map($EntityMap->Author)->ToEntity($Table->Author);
        $this->Map($EntityMap->DateOfBirth)->ToColumn($Table->DateOfBirth);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->Location)->ToColumn($Table->Location);
    }
}

?>