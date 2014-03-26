<?php

namespace Penumbra\Drivers\Base\Mapping;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\IPlatform as IRelationalPlatform;

/**
 * The mapping platform contains all specific implementaion
 * for the underlying database.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IPlatform {
    
    /**
     * @return IRelationalPlatform
     */
    public function GetRelationalPlatform();
    
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
     * @return Expressions\IAggregateMapper
     */
    public function GetAggregateMapper();
    
    /**
     * @return Expressions\IFunctionMapper
     */
    public function GetFunctionMapper();
    
    /**
     * @return Expressions\IObjectTypeMapper[]
     */
    public function GetObjectTypeMappers();
    
    /**
     * @return Expressions\IControlFlowMapper
     */
    public function GetControlFlowMapper();
    
    // <editor-fold defaultstate="collapsed" desc="Request  mappers">
    
    /**
     * Maps a given object request to the equivalent select..
     * 
     * @return Relational\ExistsSelect The exists relational select
     */
    public function MapToExistsSelect(Object\IRequest $Request, Mapping\IEntityRelationalMap $EntityRelationalMap);
    
    /**
     * Maps a given entity request to the equivalent select.
     * 
     * @return Relational\ResultSetSelect The equivalent relational select
     */
    public function MapEntityRequest(Object\IEntityRequest $EntityRequest, Mapping\IEntityRelationalMap $EntityRelationalMap);
    
    /**
     * Maps a given data request to the equivalent select.
     * 
     * @return Relational\DataSelect The data select
     */
    public function MapDataRequest(Object\IDataRequest $DataRequest, array &$AliasReviveFuncionMap, Mapping\IEntityRelationalMap $EntityRelationalMap);
    
    /**
     * Maps a supplied object procedure to the equivalent update.
     * 
     * @return Relational\Update The equivalent update
     */
    public function MapProcedure(Object\IProcedure $Procedure, Mapping\IEntityRelationalMap $EntityRelationalMap);
    
    /**
     * Maps a supplied object criteria to the equivalent delete.
     * 
     * @return Relational\Delete The equivalent relational update
     */
    public function MapCriteriaToDelete(Object\ICriteria $Criteria, Mapping\IEntityRelationalMap $EntityRelationalMap);
}

?>