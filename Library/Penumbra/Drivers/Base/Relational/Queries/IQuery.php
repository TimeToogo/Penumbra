<?php

namespace Penumbra\Drivers\Base\Relational\Queries;

interface IQuery {
    public function GetQueryString();
    
    /**
     * @return Bindings
     */
    public function GetBindings();
    public function SetBindings(Bindings $Bindings);
    
    /**
     * @return IQuery
     */
    public function Execute();
    
    /**
     * @return array
     */
    public function FetchRow();
    
    /**
     * @return array[]
     */
    public function FetchAll();
    
    /**
     * @return mixed
     */
    public function FetchValue();
}

?>
