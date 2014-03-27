<?php

namespace Penumbra\Drivers\Platforms\SQLite\Converters;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Expressions\Converters;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions as E;
use \Penumbra\Core\Relational\Expressions as EE;
use \Penumbra\Drivers\Base\Relational\Expressions\Operators as O;

final class FunctionConverter extends Converters\FunctionConverter {
    protected function MatchingFunctions() {
        return [
            'strtoupper' => 'UPPER',
            'strtolower' => 'LOWER',
            'strlen' => 'LENGTH',
            'strrev' => 'REVERSE',
            'bin2hex' => 'HEX',
            'soundex' => 'SOUNDEX',
            
            'str_replace' => 'REPLACE',
            'substr' => 'SUBSTR',
        ];
    }
    
    public function trim(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'TRIM';
        $this->TrimDefaultCharacters($ArgumentExpressions);
    }
    
    public function ltrim(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'LTRIM';
        $this->TrimDefaultCharacters($ArgumentExpressions);
    }
    
    public function rtrim(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'RTRIM';
        $this->TrimDefaultCharacters($ArgumentExpressions);
    }
    
    private function TrimDefaultCharacters(array &$ArgumentExpressions) {
        if(!isset($ArgumentExpressions[1])) {
            $ArgumentExpressions[1] = Expression::BoundValue(" \t\n\r\0\x0B");
        }
    }
}

?>