<?php

namespace Storm\Drivers\Platforms\SQLite;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Core\Relational\Expressions as EE;
use \Storm\Drivers\Base\Relational\Expressions\Operators as O;

final class FunctionMapper extends E\FunctionMapper {
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
            $ArgumentExpressions[1] = Expression::Constant(" \t\n\r\0\x0B");
        }
    }
}

?>