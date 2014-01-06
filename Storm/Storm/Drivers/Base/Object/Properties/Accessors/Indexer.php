<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class Indexer extends GetterSetter {
    public function __construct($Index) {
        parent::__construct(
                new IndexGetter($Index), 
                new IndexSetter($Index));
    }
}

?>
