<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class IndexGetter extends IndexBase implements IPropertyGetter {

    public function GetValueFrom($Entity) {
        return $Entity[$this->Index];
    }
}

?>
