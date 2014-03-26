<?php

namespace Penumbra\Drivers\Base\Relational;

abstract class RelationalTableTrait extends TableTrait {
    final protected function IsTrait(TableTrait $OtherTrait) {
        return $this->IsRelationalTrait($OtherTrait);
    }
    protected abstract function IsRelationalTrait(RelationalTableTrait $OtherTrait);
}

?>