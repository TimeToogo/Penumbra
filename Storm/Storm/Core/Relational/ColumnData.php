<?php

namespace Storm\Core\Relational;

/**
 * The base class for representing data stored in columns.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ColumnData implements \IteratorAggregate, \ArrayAccess {
    /**
     * @var IColumn[] 
     */
    private $Columns;
    
    /**
     * @var array
     */
    protected $Data;
    
    protected function __construct(array $Columns, array $Data) {
        $IndexedColumns = [];
        foreach ($Columns as $Column) {
            $IndexedColumns[$Column->GetIdentifier()] = $Column;
        }
        
        $this->Columns = $IndexedColumns;
        
        $this->Data = array_intersect_key($Data, $this->Columns);
    }
    
    /**
     * @return IColumn[] 
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return array
     */
    public function GetData() {
        return $this->Data;
    }
    
    /**
     * @return void
     */
    public function SetData(array $Data) {
        $this->Data = array_intersect_key($Data, $this->Columns);
    }
    
    /**
     * Get another column data instance with new data.
     * 
     * @param array $Data
     * @return static
     */
    public function Another(array $Data) {
        $ClonedColumnData = clone $this;
        $ClonedColumnData->Columns =& $this->Columns;
        $ClonedColumnData->SetData($Data);
        return $ClonedColumnData;
    }
    
    /**
     * Get the column with the supplied identifier
     * 
     * @param string $Identifier The column identifier
     * @return IColumn|null The matched column or null if it does not exist
     */
    final public function GetColumn($Identifier) {
        return isset($this->Columns[$Identifier]) ? $this->Columns[$Identifier] : null;
    }
    
    /**
     * Get the column with the supplied identifier
     * 
     * @param string $Identifier The column identifier
     * @return IColumn|null The matched column or null if it does not exist
     */
    final public function HasColumn(IColumn $Column) {
        return isset($this->Columns[$Column->GetIdentifier()]);
    }
        
    protected function AddColumnData(IColumn $Column, $Data) {
        $ColumnIdentifier = $Column->GetIdentifier();
        if(!isset($this->Columns[$ColumnIdentifier])) {
            throw new InvalidColumnException(
                    'The supplied column %s.%s is not part of this %s.',
                    $Column->HasTable() ? $Column->GetTable()->GetName() : '<Unknown>',
                    $Column->GetName(),
                    get_class($this));
        }
        
        $this->Data[$ColumnIdentifier] = $Data;
    }
    
    protected function RemoveColumnData(IColumn $Column) {
        unset($this->Data[$Column->GetIdentifier()]);
    }
    
    protected function HasColumnData(IColumn $Column) {
        return isset($this->Data[$Column->GetIdentifier()]);
    }
    
    protected function GetColumnData(IColumn $Column) {
        $Identifier = $Column->GetIdentifier();
        if(isset($this->Data[$Identifier]))
            return $this->Data[$Identifier];
        else
            return null;
    }
    
    final public function Hash() {
        asort($this->Data);
        return md5(json_encode($this->Data));
    }
    
    final public function HashData() {
        asort($this->Data);
        return md5(json_encode(array_values($this->Data)));
    }
    
    final public function getIterator() {
        return new \ArrayIterator($this->Data);
    }

    final public function offsetExists($Column) {
        return $this->HasColumnData($Column);
    }
    
    final public function offsetGet($Column) {
        return $this->GetColumnData($Column);
    }

    final public function offsetSet($Column, $Data) {
        $this->AddColumnData($Column, $Data);
    }

    final public function offsetUnset($Column) {
        $this->RemoveColumnData($Column);
    }
    
    /**
     * Whether or not the column data matches
     * 
     * @param ColumnData $Data The other column data
     * @return boolean
     */
    public function Matches(ColumnData $Data) {
        ksort($this->Data);
        ksort($Data->Data);
        return $this->Data === $Data->Data;
    }
}

?>