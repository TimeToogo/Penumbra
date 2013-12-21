<?php

namespace Storm\Drivers\Platforms\Development\Logging;

use Storm\Drivers\Base\Relational\Queries;

class Query implements Queries\IQuery {
    private $Logger;
    private $Query;
    
    public function __construct(ILogger $Logger, Queries\IQuery $Query) {
        $this->Logger = $Logger;
        $this->Query = $Query;
    }

    public function Execute() {
        $Log = 'Executing Query: ' . $this->GetQueryString();
        $Bindings = $this->GetBindings();
        $First = true;
        foreach($Bindings->Get() as $ParameterKey => $Binding) {
            if($First) {
                $Log .= ' - With Bindings: - (';
                $First = false;
            }
            else
               $Log .= ', ';
            
            $Log .= var_export($ParameterKey, true) . ' => ' . var_export($Binding->GetValue(), true);
        }
        if(!$First)
            $Log .= ')';
        
        $this->Logger->Log($Log);
        
        return $this->Query->Execute();
    }
    
    public function GetQueryString() {
        return $this->Query->GetQueryString();
    }

    public function FetchAll() {
        return $this->Query->FetchAll();
    }

    public function FetchRow() {
        return $this->Query->FetchRow();
    }

    public function GetBindings() {
        return $this->Query->GetBindings();
    }

    public function SetBindings(Queries\Bindings $Bindings) {
        return $this->Query->SetBindings($Bindings);
    }

}

?>