<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions\Expression;

class IndexGetter extends IndexBase implements IPropertyGetter {
    
    public function GetValueFrom($Entity) {
        return $Entity[$this->Index];
    }
}

?>
