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
    
    final public function GetColumnData() {
        return $this->ColumnData;
    }
    
    final public function GetColumn($Identifier) {
        return $this->Columns[$Identifier];
    }
    
    final public function SetColumn(IColumn $Column, $Data) {
        $this->AddColumn($Column->GetIdentifier(), $Data);
    }
    
    protected function AddColumn($ColumnIdentifier, $Data) {
        if(!isset($this->Columns[$ColumnIdentifier])) {
            throw new \InvalidArgumentException('$Column must be one of: ' . 
                    implode(', ', array_keys($this->Columns)));
        }
        
        $this->ColumnData[$ColumnIdentifier] = $Data;
    }
    
    final public function Hash() {
        return md5(serialize(asort($this->ColumnData)));
    }
    
    final public function getIterator() {
        return new \ArrayIterator($this->ColumnData);
    }

    final public function offsetExists($Column) {
        return isset($this->ColumnData[$Column->GetIdentifier()]);
    }
    
    final public function offsetGet($Column) {
        if(!$this->offsetExists($Column))
            return null;
        else
            return $this->ColumnData[$Column->GetIdentifier()];
    }

    final public function offsetSet($Column, $Data) {
        $this->SetColumn($Column, $Data);
    }

    final public function offsetUnset($Column) {
        unset($this->ColumnData[$Column->GetIdentifier()]);
    }
    
    public function Matches(ColumnData $Data) {
        foreach($this->ColumnData as $ColumnName => $Value) {
            if(!isset($Data->ColumnData[$ColumnName]))
                return false;
            if($Value !== $Data->ColumnData[$ColumnName])
                return false;
        }
        
        return true;
    }
}

?>