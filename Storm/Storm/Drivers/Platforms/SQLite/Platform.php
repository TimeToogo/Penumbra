<?php

namespace Storm\Drivers\Platforms\SQLite;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Platforms\Base;

final class Platform extends Base\Platform {
    public function __construct() {
        parent::__construct(
                false, 
                new Converters\ExpressionConverter(new Converters\FunctionConverter(), new Converters\ObjectConverter()),
                new Columns\ColumnSet(),
                new PrimaryKeys\KeyGeneratorSet(),
                new Queries\ExpressionCompiler(new Queries\ExpressionOptimizer()),
                new Queries\CriterionCompiler(),
                new Base\Queries\StandardQueryCompiler(),
                new Queries\IdentifierEscaper(),
                null, 
                null, 
                new Queries\QueryExecutor());
    }
    
    protected function IdentifiersAreCaseSensitive(Relational\Queries\IConnection $Connection) {
        return false;
    }
}

?>