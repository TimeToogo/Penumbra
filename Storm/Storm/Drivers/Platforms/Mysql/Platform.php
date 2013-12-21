<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;

final class Platform extends Relational\Platform {
    public function __construct(Relational\Queries\IConnection $Connection, $DevelopmentMode) {
        $ExpressionCompiler = new Queries\ExpressionCompiler();
        $PredicateCompiler = new Queries\PredicateCompiler($ExpressionCompiler);
        parent::__construct(
                $Connection,
                new ExpressionMapper(),
                new Columns\ColumnSet(),
                new PrimaryKeys\KeyGeneratorSet(/* TODO */),
                new Queries\RequestCompiler($ExpressionCompiler, $PredicateCompiler),
                $PredicateCompiler,
                new Queries\IdentifierEscaper(),
                $DevelopmentMode ? 
                        new Platforms\Development\Syncing\DatabaseSyncer
                                (new Syncing\DatabaseBuilder(), new Syncing\DatabaseModifier()) : 
                        new Platforms\Production\Syncing\DatabaseSyncer(),
                new Queries\QueryExecutor(),
                new Relations\ToOneReviver(), 
                new Relations\ToManyReviver());
    }
}

?>