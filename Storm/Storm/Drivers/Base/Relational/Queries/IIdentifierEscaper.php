<?php

namespace Storm\Drivers\Base\Relational\Queries;

interface IIdentifierEscaper {
    public function Escape(array $IdentifierSegments);
    public function EscapeAll(array $IdentifierSegmentsArray);
}

?>