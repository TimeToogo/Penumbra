<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core\Relational;

abstract class Database extends Relational\Database {
    /**
     * @var IPlatform
     */
    private $Platform;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @return IPlatform
     */
    final public function GetPlatform() {
        return $this->Platform;
    }
    
    final public function SetPlatform(IPlatform $Platform) {
        if($this->Platform !== null) {
            throw new Relational\RelationalException(
                    'Cannot set platform: Platform has already been set');
        }
        $this->Platform = $Platform;
    }
    
    private function VerifyPlatform($Method) {
        if(!$this->Platform === null) {
            throw new Relational\RelationalException(
                    'Call to %s requires the platform to be set',
                    $Method);
        }
    }
    
    /**
     * @return void
     */
    final public function SetConnection(Queries\IConnection $Connection) {
        if($Connection === $this->Platform->GetConnection()) {
            return;
        }
        $this->Platform->SetConnection($Connection);
        $this->Platform->Sync($this);
    }
    
    final protected function LoadResultRowData(Relational\Select $Request) {
        $this->VerifyPlatform(__METHOD__);
        return $this->Platform->Select($Request);
    }
    
    final public function CommitTransaction(Relational\Transaction $Transaction) {
        $this->VerifyPlatform(__METHOD__);
        return $this->Platform->Commit(
                $this->GetTablesOrderedByPersistingDependency(), 
                $this->GetTablesOrderedByDiscardingDependency(), 
                $Transaction);
    }
}

?>
