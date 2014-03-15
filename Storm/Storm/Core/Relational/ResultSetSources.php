<?php

namespace Storm\Core\Relational;

class ResultSetSources {
    /**
     * @var IResultSetSource
     */
    private $Source;
    
    /**
     * @var Join[] 
     */
    private $Joins = [];
    
    /**
     * @var array<string, IResultSetSource>
     */
    private $ColumnIdentifierSourceMap = [];
    
    public function __construct(IResultSetSource $Source, array $Joins = []) {
        $this->AddSourceColumns($Source);
        $this->Source = $Source;
        $this->AddJoins($Joins);
    }
    
    private function AddSourceColumns(IResultSetSource $Source) {
        foreach ($Source->GetColumns() as $Column) {
            $Identifier = $Column->GetIdentifier();
            if(isset($this->ColumnIdentifierSourceMap[$Identifier])) {
                throw new RelationalException(
                        'Ambiguous result set source for column: %s',
                        $Identifier);
            }
            
            $this->ColumnIdentifierSourceMap[$Column] = $Source;
        }
    }
    
    /**
     * @return IResultSetSource[]
     */
    final public function GetSource() {
        return $this->Source;
    }
    
    /**
     * @return Join[]
     */
    final public function GetJoins() {
        return $this->Joins;
    }
    
    /**
     * Add a joined table to the criteria.
     * 
     * @param Join $Join The join to add
     * @return void
     */
    final public function AddJoin(Join $Join) {
        $this->AddSourceColumns($Join->GetSource());
        $this->Joins[] = $Join;
    }
    
    /**
     * Add multiple joined tables to the criteria.
     * 
     * @param array $Joins The joins to add
     * @return void
     */
    final public function AddJoins(array $Joins) {
        foreach ($Joins as $Join) {
            $this->AddJoin($Join);
        }
    }

    /**
     * @return boolean
     */
    final public function IsJoined() {
        return count($this->Joins) > 0;
    }
    
    /**
     * @return boolean
     */
    final public function ColumnHasSource(IColumn $Column) {
        return isset($this->ColumnIdentifierSourceMap[$Column->GetIdentifier()]);
    }
    
    /**
     * @return ISource|null
     */
    final public function GetColumnSource(IColumn $Column) {
        $Identifier = $Column->GetIdentifier();
        return isset($this->ColumnIdentifierSourceMap[$Identifier]) ? 
                $this->ColumnIdentifierSourceMap[$Identifier] : null;
    }
    
}

?>