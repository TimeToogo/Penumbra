<?php

namespace Storm\Drivers\Platforms\SQLite;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Platforms\Base;

final class Platform extends Base\Platform {
    public function __construct($DevelopmentMode) {
        parent::__construct(
                $DevelopmentMode, 
                new ExpressionMapper(new FunctionMapper(), new ObjectMapper()),
                new Columns\ColumnSet(),
                new PrimaryKeys\KeyGeneratorSet(),
                new Queries\ExpressionCompiler(new Queries\ExpressionOptimizer()),
                new Queries\CriterionCompiler(),
                new Queries\IdentifierEscaper(),
                new Syncing\DatabaseBuilder(), 
                new Syncing\DatabaseModifier(), 
                new Queries\QueryExecutor());
    }
    
    protected function IdentifiersAreCaseSensitive(Relational\Queries\IConnection $Connection) {
        return false;
    }
}

?>