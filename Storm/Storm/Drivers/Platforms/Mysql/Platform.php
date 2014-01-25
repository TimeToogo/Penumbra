<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;

final class Platform extends Relational\Platform {
    private $DevelopmentMode;
    
    public function __construct($DevelopmentMode) {
        $this->DevelopmentMode = $DevelopmentMode;        
        parent::__construct(
                new ExpressionMapper(new FunctionMapper(), new ObjectMapper()),
                new Columns\ColumnSet(),
                new PrimaryKeys\KeyGeneratorSet(),
                new Queries\ExpressionCompiler(new Queries\ExpressionOptimizer()),
                new Queries\CriterionCompiler(),
                new Queries\IdentifierEscaper(),
                $DevelopmentMode ? 
                        new Platforms\Development\Syncing\DatabaseSyncer
                                (new Syncing\DatabaseBuilder(), new Syncing\DatabaseModifier(), true) : 
                        new Platforms\Production\Syncing\DatabaseSyncer(),
                new Queries\QueryExecutor());
    }
    
    protected function OnSetConnection(Relational\Queries\IConnection $Connection) {
        if($this->DevelopmentMode) {
            $IdentifiersAreCaseSensitive = 
                    ((int)$Connection->FetchValue('SELECT @@lower_case_table_names')) === 0;
            $this->GetDatabaseSyncer()->SetIdentifiersAreCaseSensitive($IdentifiersAreCaseSensitive);
        }
    }
}

?>