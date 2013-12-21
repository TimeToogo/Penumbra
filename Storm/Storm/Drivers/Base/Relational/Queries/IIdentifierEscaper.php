<?php

namespace Storm\Drivers\Base\Relational\Queries;

interface IIdentifierEscaper {
    public function Escape(array $IdentifierSegments);
    public function EscapeAll(array $IdentifierSegmentsArray);
    public function Alias($EscapedIdentifier, $Alias);
    public function AliasAll(array $EscapedIdentifierAliasMap);
    public function EscapeAndAliasAll(array $AliasIdentifierSegmentsMap);
}

?>