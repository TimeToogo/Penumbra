<?php

namespace Storm\Drivers\Platforms\PDO;

use Storm\Drivers\Base\Relational\Queries;

class Query implements Queries\IQuery {
    private $Bindings;
    private $HasBound = false;
    private $Statement;
    
    public function __construct(\PDOStatement $Statement, Queries\Bindings $Bindings = null) {
        $this->Statement = $Statement;
        
        if($Bindings === null) {
            $Bindings = new Queries\Bindings();
        }
        $this->Bindings = $Bindings;
    }
    
    public function GetQueryString() {
        return $this->Statement->queryString;
    }

    public function GetBindings() {
        return $this->Bindings;
    }
    public function SetBindings(Queries\Bindings $Bindings) {
        $this->Bindings = $Bindings;
    }
    
    public function Execute() {
        $this->BindAll();
        
        $this->Statement->execute();
        
        return $this;
    }
    
    public function FetchAll() {
        return $this->Statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function FetchRow() {
        return $this->Statement->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function FetchValue() {
        return $this->Statement->fetchColumn();
    }
    
    private function BindAll() {
        foreach($this->Bindings->Get() as $ParameterKey => $Binding) {
            //PDO positional parameters are one based
            if(is_int($ParameterKey)) {
                $ParameterKey++;
            }
            $this->Bind($ParameterKey, $Binding->GetValue(), $Binding->GetParameterType());
        }
        $this->HasBound = true;
    }
    
    private function Bind($To, $Value, $ParameterType) {
        $PDOParameterType = PDOParameterType::MapParameterType($ParameterType);
        return $this->Statement->bindValue($To, $Value, $PDOParameterType);
    }
}

?>