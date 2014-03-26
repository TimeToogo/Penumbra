<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions\Expression;

class IndexGetter extends IndexBase implements IPropertyGetter {
    
    public function GetValueFrom($Entity) {
        return $Entity[$this->Index];
    }
}

?>
