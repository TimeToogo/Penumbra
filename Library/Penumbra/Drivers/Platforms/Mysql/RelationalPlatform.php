<?php

namespace Penumbra\Drivers\Platforms\Mysql;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Platforms\Standard;

final class RelationalPlatform extends Standard\RelationalPlatform {
    public function __construct($DevelopmentMode = false) {
        parent::__construct(
                $DevelopmentMode, 
                new Columns\ColumnSet(),
                new PrimaryKeys\KeyGeneratorSet(),
                new Queries\ExpressionCompilerVisitor(new Queries\ExpressionOptimizerWalker()),
                new Queries\QueryCompiler(),
                new Queries\RowPersister(),
                new Queries\IdentifierEscaper(),
                new Syncing\DatabaseBuilder(), 
                new Syncing\DatabaseModifier());
    }
    
    protected function IdentifiersAreCaseSensitive(Relational\Queries\IConnection $Connection) {
        return ((int)$Connection->FetchValue('SELECT @@lower_case_table_names')) === 0;
    }
}

?>