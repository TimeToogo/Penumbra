<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

final class Mode {
    private function __construct() {}
    
    const Eager = 0;
    const GlobalScopeLazy = 1;
    const RequestScopeLazy = 2;
    const ParentScopeLazy = 3;
}

?>