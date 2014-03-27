<?php

namespace Penumbra\Drivers\Base\Relational;

abstract class StructuralTableTrait extends TableTrait {
    final protected function IsTrait(TableTrait $OtherTrait) {
        return $this->IsStructuralTrait($OtherTrait);
    }
    protected abstract function IsStructuralTrait(StructuralTableTrait $OtherTrait);
}

?>