<?php

namespace Storm\Core\Object;

interface IProperty {
    const IPropertyType = __CLASS__;
    
    public function GetName();
    /**
     * @return EntityMap
     */
    public function GetEntityMap();
    public function HasEntityMap();
    public function SetEntityMap(EntityMap $EntityMap = null);
    
    public function State(State $State, $Entity);
    public function Persist(UnitOfWork $UnitOfWork, $Entity);
    public function Discard(UnitOfWork $UnitOfWork, $Entity);
    public function Revive($Entity, &$Value);
}

?>