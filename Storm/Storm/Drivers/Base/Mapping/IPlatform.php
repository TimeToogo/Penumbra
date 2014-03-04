<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expressions as R;

/**
 * The mapping platform contains all specific expression mapping implementaion
 * for the underlying database.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IPlatform {
    
    /**
     * @return Expressions\IValueMapper
     */
    public function GetValueMapper();
    
    /**
     * @return Expressions\IArrayMapper
     */
    public function GetArrayMapper();
        
    /**
     * @return Expressions\IOperationMapper
     */
    public function GetOperationMapper();
    
    /**
     * @return Expressions\IFunctionMapper
     */
    public function GetFunctionMapper();
    
    /**
     * @return Expressions\IObjectMapper
     */
    public function GetObjectMapper();
    
    /**
     * @return Expressions\IResourceMapper
     */
    public function GetResourceMapper();
    
    /**
     * @return Expressions\IControlFlowMapper
     */
    public function GetControlFlowMapper();
    
    /**
     * @return R\Expression
     */
    public function MapExpression(O\Expression $Expression);
}

?>