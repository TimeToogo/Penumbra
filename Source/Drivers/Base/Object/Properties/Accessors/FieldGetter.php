<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions\Expression;

class FieldGetter extends FieldBase implements IPropertyGetter {
    
    public function GetValueFrom($Entity) {
        return $this->Reflection->getValue($Entity);
    }
}

?>
