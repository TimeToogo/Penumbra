<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Platforms\Base;

final class Platform extends Base\Platform {
    public function __construct($DevelopmentMode = false) {
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
        return ((int)$Connection->FetchValue('SELECT @@lower_case_table_names')) === 0;
    }
}

?>