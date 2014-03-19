<?php

namespace Storm\Drivers\Platforms\Development\Logging;

use Storm\Drivers\Base\Relational\Queries;

class Query implements Queries\IQuery {
    private $Logger;
    private $Query;
    private $TimeSpentQuerying;
    
    public function __construct(ILogger $Logger, Queries\IQuery $Query, &$TimeSpentQuerying) {
        $this->Logger = $Logger;
        $this->Query = $Query;
        $this->TimeSpentQuerying =& $TimeSpentQuerying;
    }
    
    public function Execute() {
        $Bindings = $this->GetBindings();
        
        $this->Logger->Log(
                'Executing Query: ' . $this->InterpolateQuery($this->GetQueryString(), $Bindings->Get()));
        
        $Start = microtime(true);
        $Result = $this->Query->Execute();
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Result;
    }
    
   /**
    * Replaces any parameter placeholders in a query with the value of that
    * parameter. Useful for debugging. Assumes anonymous parameters from 
    * $params are are in the same order as specified in $query
    *
    * @param string $Query The sql query with parameter placeholders
    * @param Queries\Binding[] $Params The array of substitution parameters
    * @return string The interpolated query
    */
   public function InterpolateQuery($Query, array $Params) {
       $QuerySegments = explode('?', $Query);
       $InterpolatedQuery = array_shift($QuerySegments);
       $Count = 0;
       foreach ($Params as $Key => $Binding) {
           $ParameterType = $Binding->GetParameterType();
           $Value = $Binding->GetValue();
           switch ($ParameterType) {
               case Queries\ParameterType::String:
                   $Value = "'" . $Value . "'";
                   break;
               case Queries\ParameterType::Double:
               case Queries\ParameterType::Integer:
                   $Value = (string)$Value;
                   break;
               case Queries\ParameterType::Stream:
                   $Value = 'Rescource: ' . get_resource_type($Value);
                   break;
               case Queries\ParameterType::Boolean:
                   $Value = $Value ? 'TRUE' : 'FALSE';
                   break;
               case Queries\ParameterType::Null:
                   $Value = 'NULL';
                   break;
               default:
                   throw new \Exception;
           }
           
           $InterpolatedQuery .= $Value . array_shift($QuerySegments);
           $Count++;
       }
       
       return $InterpolatedQuery;
   }
    
    public function GetQueryString() {
        return $this->Query->GetQueryString();
    }

    public function FetchAll() {
        $Start = microtime(true);
        $Result = $this->Query->FetchAll();
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Result;
    }

    public function FetchRow() {
        $Start = microtime(true);
        $Result = $this->Query->FetchRow();
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Result;
    }

    public function FetchValue() {
        $Start = microtime(true);
        $Result = $this->Query->FetchValue();
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Result;
    }

    public function GetBindings() {
        return $this->Query->GetBindings();
    }

    public function SetBindings(Queries\Bindings $Bindings) {
        return $this->Query->SetBindings($Bindings);
    }

}

?>