<?php

namespace Storm\Drivers\Platforms\Development\Logging;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;

class Connection implements Queries\IConnection {
    private $Logger;
    private $Connection;
    
    public function __construct(ILogger $Logger, Queries\IConnection $Connection) {
        $this->Logger = $Logger;
        $this->Connection = $Connection;
    }

    public function BeginTransaction() {
        $this->Logger->Log('Beginning transaction');
        return $this->Connection->BeginTransaction();
    }

    public function CommitTransaction() {
        $this->Logger->Log('Commiting transaction');
        return $this->Connection->CommitTransaction();
    }

    public function RollbackTransaction() {
        $this->Logger->Log('Rolling back transaction');
        return $this->Connection->RollbackTransaction();
    }

    public function Disconnect() {
        return $this->Connection->Disconnect();
    }

    public function Escape($Value, $ParameterType) {
        return $this->Connection->Escape($Value, $ParameterType);
    }

    public function Execute($QueryString, Queries\Bindings $Bindings = null) {
        $this->Logger->Log('Executing plain query: ' . $QueryString);
        return $this->Connection->Execute($QueryString, $Bindings);
    }

    public function FetchValue($QueryString, Queries\Bindings $Bindings = null) {
        $this->Logger->Log('Fetching value with query: ' . $QueryString);
        return $this->Connection->FetchValue($QueryString);
    }

    public function GetLastInsertIncrement() {
        return $this->Connection->GetLastInsertIncrement();
    }

    public function IsInTransaction() {
        return $this->Connection->IsInTransaction();
    }
    
    public function Prepare($QueryString, Queries\Bindings $Bindings = null) {
        return new Query($this->Logger, $this->Connection->Prepare($QueryString, $Bindings));
    }

    public function QueryBuilder(Queries\Bindings $Bindings = null) {
        $QueryBuilder = $this->Connection->QueryBuilder($Bindings);
        return new Queries\QueryBuilder(
                $this, 
                $QueryBuilder->GetParameterPlaceholder(), 
                $QueryBuilder->GetBindings(),
                $QueryBuilder->GetExpressionCompiler(),
                $QueryBuilder->GetCriterionCompiler(),
                $QueryBuilder->GetIdentifierEscaper());
    }

    public function SetIdentifierEscaper(Queries\IIdentifierEscaper $IdentifierEscaper) {
        return $this->Connection->SetIdentifierEscaper($IdentifierEscaper);
    }

    public function SetCriterionCompiler(Queries\ICriterionCompiler $RequestCompiler) {
        return $this->Connection->SetCriterionCompiler($RequestCompiler);
    }

    public function SetExpressionCompiler(Queries\IExpressionCompiler $ExpressionCompiler) {
        return $this->Connection->SetExpressionCompiler($ExpressionCompiler);
    }
}

?>