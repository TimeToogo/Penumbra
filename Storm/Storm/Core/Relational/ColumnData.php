<?php

namespace Storm\Core\Relational;

abstract class ColumnData implements \IteratorAggregate, \ArrayAccess {
    private $Columns = array();
    private $ColumnData;
    
    protected function __construct(array $Columns, array $ColumnData) {
        foreach ($Columns as $Column) {
            $this->Columns[$Column->GetIdentifier()] = $Column;
        }
        $this->ColumnData = $ColumnData;
    }
    
    final public function GetColumns() {
        return $this->Columns;
    }
    
    final public function GetColumnData() {
        return $this->ColumnData;
    }
    
    final public function Another(array $ColumnData) {
        $ClonedColumnData = clone $this;
        $ClonedColumnData->ColumnData = $ColumnData;
        return $ClonedColumnData;
    }
    
    final public function GetColumn($Identifier) {
        return $this->Columns[$Identifier];
    }
    
    final public function SetColumn(IColumn $Column, $Data) {
        $this->AddColumn($Column, $Data);
    }
    
    final public function SetData(ColumnData $ColumnData) {
        foreach($ColumnData as $Identifier => $Data) {
            $Column = $ColumnData->GetColumn($Identifier);
            $this->AddColumn($Column, $Data);
        }
    }
    
    protected function AddColumn(IColumn $Column, $Data) {
        $ColumnIdentifier = $Column->GetIdentifier();
        if(!isset($this->Columns[$ColumnIdentifier])) {
            throw new \InvalidArgumentException('$Column must be one of: ' . 
                    implode(', ', array_keys($this->Columns)));
        }
        
        $this->ColumnData[$ColumnIdentifier] = $Data;
    }
    
    protected function RemoveColumn(IColumn $Column) {
        unset($this->ColumnData[$Column->GetIdentifier()]);
    }
    
    final public function Hash() {
        asort($this->ColumnData);
        return md5(json_encode($this->ColumnData));
    }
    
    final public function HashData() {
        asort($this->ColumnData);
        return md5(json_encode(array_values($this->ColumnData)));
    }
    
    final public function getIterator() {
        return new \ArrayIterator($this->ColumnData);
    }

    final public function offsetExists($Column) {
        return isset($this->ColumnData[$Column->GetIdentifier()]);
    }
    
    final public function offsetGet($Column) {
        $Null = null;
        if(!$this->offsetExists($Column))
            return $Null;
        else
            return $this->ColumnData[$Column->GetIdentifier()];
    }

    final public function offsetSet($Column, $Data) {
        $this->AddColumn($Column, $Data);
    }

    final public function offsetUnset($Column) {
        $this->RemoveColumn($Column);
    }
    
    public function Matches(ColumnData $Data) {
        ksort($PropertyData);
        return $this->ColumnData === $Data->ColumnData;
    }
}

?>