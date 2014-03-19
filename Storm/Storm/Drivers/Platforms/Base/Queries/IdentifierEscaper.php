<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Drivers\Base\Relational\Queries;

class IdentifierEscaper implements Queries\IIdentifierEscaper {
    private $IdentifierCharacter;
    private $EscapeCharacter;
    private $IdentifierSeparator;
    private $EscapedIdentifier;
    
    public function __construct($IdentifierCharacter, $EscapeCharacter, $IdentifierSeparator) {
        $this->IdentifierCharacter = $IdentifierCharacter;
        $this->EscapeCharacter = $EscapeCharacter;
        $this->IdentifierSeparator = $IdentifierSeparator;
        $this->EscapedIdentifier = $EscapeCharacter . $IdentifierCharacter;
    }
    
    public function Quote(&$Identifier) {
        $Identifier = $this->IdentifierCharacter . $Identifier . $this->IdentifierCharacter;
    }
    
    public function QualifyAll(array $Identifiers) {
        $Identifiers = str_replace($this->IdentifierCharacter, $this->EscapedIdentifier, $Identifiers);
        array_walk($Identifiers, [$this, 'Quote']);
        
        return $Identifiers;
    }
    
    public function Qualify($Identifier) {
        $Identifier = str_replace($this->IdentifierCharacter, $this->EscapedIdentifier, $Identifier);
        $this->Quote($Identifier);
        return $Identifier;
    }

    public function Escape(array $IdentifierSegments) {
        $EscapedIdentifier = $this->QualifyAll($IdentifierSegments);
        
        return implode($this->IdentifierSeparator, $EscapedIdentifier);
    }
    
    public function EscapeAll(array $IdentifierSegmentsArray) {
        return array_map([$this, 'Escape'], $IdentifierSegmentsArray);
    }
}

?>