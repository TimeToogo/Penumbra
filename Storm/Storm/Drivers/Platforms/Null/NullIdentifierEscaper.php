<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational\Queries;

final class NullIdentifierEscaper implements Queries\IIdentifierEscaper {
    public function Alias($EscapedIdentifier, $Alias) {
        
    }

    public function AliasAll(array $EscapedIdentifierAliasMap) {
        
    }

    public function Escape(array $IdentifierSegments) {
        
    }

    public function EscapeAll(array $IdentifierSegmentsArray) {
        
    }

    public function EscapeAndAliasAll(array $AliasIdentifierSegmentsMap) {
        
    }

}

?>