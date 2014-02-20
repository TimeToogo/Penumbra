<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

final class LoadingMode {
    private function __construct() {}
    
    const Eager = 0;
    const SemiLazy = 1;
    const Lazy = 2;
    const ExtraLazy = 3;
}

?>