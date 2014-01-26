<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Drivers\Base\Relational\Queries;

abstract class IdentifierEscaper implements Queries\IIdentifierEscaper {
    private $IdentifierCharacter;
    private $EscapeCharacter;
    private $IdentifierSeparator;
    private $AliasKeyword;
    private $EscapedIdentifier;
    
    public function __construct($IdentifierCharacter, $EscapeCharacter, $IdentifierSeparator, $AliasKeyword) {
        $this->IdentifierCharacter = $IdentifierCharacter;
        $this->EscapeCharacter = $EscapeCharacter;
        $this->IdentifierSeparator = $IdentifierSeparator;
        $this->AliasKeyword = $AliasKeyword;
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

    public function Alias($EscapedIdentifier, $Alias) {
        return $EscapedIdentifier . ' ' . $this->AliasKeyword . ' ' . $this->Qualify($Alias);
    }
    
    public function AliasAll(array $EscapedIdentifierAliasMap) {
        $EscapedIdentifierEscapedAliasMap = $this->QualifyAll($EscapedIdentifierAliasMap);
        $AliasedValues = array();
        foreach($EscapedIdentifierEscapedAliasMap as $EscapedIdentifier => $EscapedAlias) {
            $AliasedValues[] = $EscapedIdentifier . ' ' . $this->AliasKeyword . ' ' . $EscapedAlias;
        }
        
        return $AliasedValues;
    }
    
    public function EscapeAndAliasAll(array $AliasIdentifierSegmentsMap) {
        $EscapedIdentifiers = array_combine($this->AliasAll(array_keys($AliasIdentifierSegmentsMap)), $this->EscapeAll($AliasIdentifierSegmentsMap));
        $AliasedValues = array();
        foreach($EscapedIdentifiers as $EscapedIdentifier => $EscapedAlias) {
            $AliasedValues[] = $EscapedIdentifier . ' ' . $this->AliasKeyword . ' ' . $EscapedAlias;
        }
        
        return $AliasedValues;
    }
}

?>