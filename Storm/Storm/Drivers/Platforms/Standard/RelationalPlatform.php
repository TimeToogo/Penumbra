<?php

namespace Storm\Drivers\Platforms\Standard;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;

abstract class RelationalPlatform extends Relational\Platform {
    private $DevelopmentMode;
    public function __construct(
            $DevelopmentMode = false,
            Relational\Columns\IColumnSet $ColumnSet, 
            Relational\PrimaryKeys\IKeyGeneratorSet $KeyGeneratorSet, 
            Relational\Queries\IExpressionCompiler $ExpressionCompilerWalker, 
            Relational\Queries\IQueryCompiler $QueryCompiler,
            Relational\Queries\IRowPersister $RowPersister,
            Relational\Queries\IIdentifierEscaper $IdentifierEscaper,
            Relational\Syncing\IDatabaseBuilder $DatabaseBuilder = null,
            Relational\Syncing\IDatabaseModifier $DatabaseModifier = null) {
        $this->DevelopmentMode = $DevelopmentMode;
        
        parent::__construct(
                $ColumnSet, 
                $KeyGeneratorSet, 
                $ExpressionCompilerWalker,
                $QueryCompiler, 
                $IdentifierEscaper, 
                $DevelopmentMode ? 
                        new Platforms\Development\Syncing\DatabaseSyncer
                                ($DatabaseBuilder, $DatabaseModifier, true) : 
                        new Platforms\Production\Syncing\DatabaseSyncer(),
                new Relational\Queries\TransactionCommiter($RowPersister));
    }
    
    protected function OnSetConnection(Relational\Queries\IConnection $Connection) {
        if($this->DevelopmentMode) {
            $IdentifiersAreCaseSensitive = $this->IdentifiersAreCaseSensitive($Connection);
            $this->GetDatabaseSyncer()->SetIdentifiersAreCaseSensitive($IdentifiersAreCaseSensitive);
        }
    }
    protected abstract function IdentifiersAreCaseSensitive(Relational\Queries\IConnection $Connection);
}

?>