<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;

final class Platform extends Relational\Platform {
    public function __construct(Relational\Queries\IConnection $Connection, $DevelopmentMode) {
        if($DevelopmentMode) {
            $IdentifiersAreCaseSensitive = 
                    ((int)$Connection->FetchValue('SELECT @@lower_case_table_names')) === 0;
        }
        parent::__construct(
                $Connection,
                new ExpressionMapper(new FunctionMapper(), new ObjectMapper()),
                new Columns\ColumnSet(),
                new PrimaryKeys\KeyGeneratorSet(/* TODO */),
                new Queries\ExpressionCompiler(new Queries\ExpressionOptimizer()),
                new Queries\RequestCompiler(),
                new Queries\PredicateCompiler(),
                new Queries\IdentifierEscaper(),
                $DevelopmentMode ? 
                        new Platforms\Development\Syncing\DatabaseSyncer
                                (new Syncing\DatabaseBuilder(), new Syncing\DatabaseModifier(), $IdentifiersAreCaseSensitive) : 
                        new Platforms\Production\Syncing\DatabaseSyncer(),
                new Queries\QueryExecutor());
    }
}

?>