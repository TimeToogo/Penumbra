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
        return $this->Connection->BeginTransaction();
    }

    public function CommitTransaction() {
        return $this->Connection->CommitTransaction();
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

    public function LoadRows(Relational\Table $Table, Queries\IQuery $Query) {
        return $this->Connection->LoadRows($Table, $Query);
    }
    
    public function LoadJoinedRows(array $JoinedTables, Queries\IQuery $Query) {
        return $this->Connection->LoadJoinedRows($JoinedTables, $Query);
    }
    
    public function Prepare($QueryString, Queries\Bindings $Bindings = null) {
        $this->Logger->Log('Preparing query: ' . $QueryString);
        return new Query($this->Logger, $this->Connection->Prepare($QueryString, $Bindings));
    }

    public function QueryBuilder(Queries\Bindings $Bindings = null) {
        $QueryBuilder = $this->Connection->QueryBuilder($Bindings);
        return new Queries\QueryBuilder(
                $this, 
                $QueryBuilder->GetParameterPlaceholder(), 
                $QueryBuilder->GetBindings(),
                $QueryBuilder->GetRequestCompiler(),
                $QueryBuilder->GetPredicateCompiler(),
                $QueryBuilder->GetIdentifierEscaper());
    }

    public function RollbackTransaction() {
        return $this->Connection->RollbackTransaction();
    }

    public function SetIdentifierEscaper(Queries\IIdentifierEscaper $IdentifierEscaper) {
        return $this->Connection->SetIdentifierEscaper($IdentifierEscaper);
    }

    public function SetPredicateCompiler(Queries\IPredicateCompiler $PredicateCompiler) {
        return $this->Connection->SetPredicateCompiler($PredicateCompiler);
    }

    public function SetRequestCompiler(Queries\IRequestCompiler $RequestCompiler) {
        return $this->Connection->SetRequestCompiler($RequestCompiler);
    }

}

?>