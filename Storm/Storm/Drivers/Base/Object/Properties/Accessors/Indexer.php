<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class Indexer extends GetterSetter {
    private $Index;
    public function __construct($Index) {
        $this->Index = $Index;
        parent::__construct(
                new IndexGetter($Index), 
                new IndexSetter($Index));
    }
    
    public function GetIndex() {
        return $this->Index;
    }
}

?>
