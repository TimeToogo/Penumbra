<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Drivers\Base\Relational\Queries;

final class IdentifierEscaper implements Queries\IIdentifierEscaper {
    public function Quote(&$Identifier) {
        $Identifier = '`' . $Identifier . '`';
    }
    
    public function QualifyAll(array $Identifiers) {
        $Identifiers = str_replace('`', '``', $Identifiers);
        array_walk($Identifiers, [$this, 'Quote']);
        
        return $Identifiers;
    }
    
    public function Qualify($Identifier) {
        $Identifier = str_replace('`', '``', $Identifier);
        $this->Quote($Identifier);
        return $Identifier;
    }

    public function Escape(array $IdentifierSegments) {
        $EscapedIdentifier = $this->QualifyAll($IdentifierSegments);
        
        return implode('.', $EscapedIdentifier);
    }
    
    public function EscapeAll(array $IdentifierSegmentsArray) {
        return array_map([$this, 'Escape'], $IdentifierSegmentsArray);
    }

    public function Alias($EscapedIdentifier, $Alias) {
        return $EscapedIdentifier . ' AS ' . $this->Qualify($Alias);
    }
    
    public function AliasAll(array $EscapedIdentifierAliasMap) {
        $EscapedIdentifierEscapedAliasMap = $this->QualifyAll($EscapedIdentifierAliasMap);
        $AliasedValues = array();
        foreach($EscapedIdentifierEscapedAliasMap as $EscapedIdentifier => $EscapedAlias) {
            $AliasedValues[] = $EscapedIdentifier . ' AS ' . $EscapedAlias;
        }
        
        return $AliasedValues;
    }
    
    public function EscapeAndAliasAll(array $AliasIdentifierSegmentsMap) {
        $EscapedIdentifiers = array_combine($this->AliasAll(array_keys($AliasIdentifierSegmentsMap)), $this->EscapeAll($AliasIdentifierSegmentsMap));
        $AliasedValues = array();
        foreach($EscapedIdentifiers as $EscapedIdentifier => $EscapedAlias) {
            $AliasedValues[] = $EscapedIdentifier . ' AS ' . $EscapedAlias;
        }
        
        return $AliasedValues;
    }
}

?>