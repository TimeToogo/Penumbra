<?php

namespace Penumbra\Drivers\Base\Mapping;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\IPlatform as IRelationalPlatform;

class Platform implements IPlatform {
    
    /**
     * @var IRelationalPlatform
     */
    private $RelationalPlatform;
    
    /**
     * @var Expressions\IValueMapper
     */
    private $ValueMapper;
    
    /**
     * @var Expressions\IArrayMapper
     */
    private $ArrayMapper;
    
    /**
     * @var Expressions\IOperationMapper
     */
    private $OperationMapper;
    
    /**
     * @var Expressions\IFunctionMapper
     */
    private $FunctionMapper;
    
    /**
     * @var Expressions\IAggregateMapper
     */
    private $AggregateMapper;
    
    /**
     * @var Expressions\IObjectTypeMapper[]
     */
    private $ObjectTypeMappers;
    
    /**
     * @var Expressions\IControlFlowMapper
     */
    private $ControlFlowMapper;
    
    /**
     * @var Expressions\RequestMapper
     */
    private $RequestMapper;
    
    /**
     * @var Queries\ProcedureMapper
     */
    private $ProcedureMapper;
    
    /**
     * @var Queries\DeleteMapper
     */
    private $DeleteMapper;
    
    public function __construct(
            IRelationalPlatform $RelationalPlatform,
            Expressions\IValueMapper $ValueMapper, 
            Expressions\IArrayMapper $ArrayMapper, 
            Expressions\IOperationMapper $OperationMapper,
            Expressions\IFunctionMapper $FunctionMapper, 
            Expressions\IAggregateMapper $AggregateMapper, 
            array $ObjectTypeMappers,
            Expressions\IControlFlowMapper $ControlFlowMapper) {
        $this->RelationalPlatform = $RelationalPlatform;
        $this->ValueMapper = $ValueMapper;
        $this->ArrayMapper = $ArrayMapper;
        $this->OperationMapper = $OperationMapper;
        $this->FunctionMapper = $FunctionMapper;
        $this->AggregateMapper = $AggregateMapper;
        array_walk($ObjectTypeMappers, function ($I) { $this->AddObjectTypeMapper($I); });
        $this->ControlFlowMapper = $ControlFlowMapper;
        
        $this->RequestMapper = new Queries\RequestMapper();
        $this->ProcedureMapper = new Queries\ProcedureMapper();
        $this->DeleteMapper = new Queries\DeleteMapper();
    }
    
    private function AddObjectTypeMapper(Expressions\IObjectTypeMapper $ObjectTypeMapper) {
        $this->ObjectTypeMappers[$ObjectTypeMapper->GetClassType()] = $ObjectTypeMapper;
    }
    
    public function GetRelationalPlatform() {
        return $this->RelationalPlatform;
    }

    final public function GetValueMapper() {
        return $this->ValueMapper;
    }
    
    final public function GetArrayMapper() {
        return $this->ArrayMapper;
    }

    final public function GetOperationMapper() {
        return $this->OperationMapper;
    }

    final public function GetFunctionMapper() {
        return $this->FunctionMapper;
    }
    
    final public function GetAggregateMapper() {
        return $this->AggregateMapper;
    }

    final public function GetObjectTypeMappers() {
        return $this->ObjectTypeMappers;
    }
    
    final public function GetControlFlowMapper() {
        return $this->ControlFlowMapper;
    }

    final public function MapToExistsSelect(Object\IRequest $Request, Mapping\IEntityRelationalMap $EntityRelationalMap) {
        $ExistsSelect = new Relational\ExistsSelect($this->GetResultSetSpecification($Request, $EntityRelationalMap));
        
        $this->RequestMapper->MapRequestToExistsSelect(
                $Request, 
                $ExistsSelect,
                $this->GetExpressionMapper($EntityRelationalMap, $ExistsSelect));
        
        return $ExistsSelect;
    }

    final public function MapEntityRequest(Object\IEntityRequest $EntityRequest, Mapping\IEntityRelationalMap $EntityRelationalMap) {
        $ResultSetSelect = new Relational\ResultSetSelect($this->GetResultSetSpecification($EntityRequest, $EntityRelationalMap));
        
        $this->RequestMapper->MapEntityRequest(
                $EntityRequest, 
                $ResultSetSelect, 
                $EntityRelationalMap, 
                $this->GetExpressionMapper($EntityRelationalMap, $ResultSetSelect));
        
        return $ResultSetSelect;
    }
    
    final public function MapDataRequest(Object\IDataRequest $DataRequest, array &$AliasReviveFuncionMap, Mapping\IEntityRelationalMap $EntityRelationalMap) {
        $DataSelect = new Relational\DataSelect([], $this->GetResultSetSpecification($DataRequest, $EntityRelationalMap));
        
        $AliasReturnTypes = [];
        $this->RequestMapper->MapDataRequest(
                $DataRequest, 
                $DataSelect, 
                $AliasReturnTypes,
                $this->GetExpressionMapper($EntityRelationalMap, $DataSelect));
        
        foreach ($AliasReturnTypes as $Alias => $ReturnType) {
            if($ReturnType !== null) {
                $AliasReviveFuncionMap[$Alias] = [$this->ObjectTypeMappers[$ReturnType], 'ReviveInstance'];
            }
        }
        
        return $DataSelect;
    }

    final public function MapProcedure(Object\IProcedure $Procedure, Mapping\IEntityRelationalMap $EntityRelationalMap) {
        $Update = new Relational\Update($this->GetResultSetSpecification($Procedure, $EntityRelationalMap));
        
        $this->ProcedureMapper->MapProcedure(
                $Procedure, 
                $Update, 
                $this->GetExpressionMapper($EntityRelationalMap, $Update));
        
        return $Update;
    }
    
    final public function MapCriteriaToDelete(Object\ICriteria $ObjectCriteria, Mapping\IEntityRelationalMap $EntityRelationalMap) {
        $Delete = new Relational\Delete(new Relational\ResultSetSpecification(
                    $EntityRelationalMap->GetSelectSources(),
                    $EntityRelationalMap->GetSelectCriteria()));
        
        $this->DeleteMapper->MapCriteriaToDelete(
                $ObjectCriteria, 
                $Delete, 
                $EntityRelationalMap, 
                $this->GetExpressionMapper($EntityRelationalMap, $Delete));
        
        return $Delete;
    }

    
    private function GetResultSetSpecification(
            Object\IQuery $Query, 
            Mapping\IEntityRelationalMap $EntityRelationalMap) {
        
        if($Query->IsFromEntityRequest()) {
            $SubEntitySelect = $this->MapEntityRequest($Query->GetFromEntityRequest(), $EntityRelationalMap);
            return new Relational\ResultSetSpecification(
                    new Relational\ResultSetSources($SubEntitySelect),
                    new Relational\Criteria());
        }
        else {
            return new Relational\ResultSetSpecification(
                    $EntityRelationalMap->GetSelectSources(),
                    $EntityRelationalMap->GetSelectCriteria());
        }
    }    
    
    private function GetExpressionMapper(Mapping\IEntityRelationalMap $EntityRelationalMap, Relational\Query $Query) {
        return new ExpressionMapper(
                new PropertyExpressionResolver($Query->GetResultSetSpecification(), $EntityRelationalMap), 
                $this->ValueMapper, 
                $this->ArrayMapper, 
                $this->OperationMapper, 
                $this->FunctionMapper, 
                $this->AggregateMapper,
                $this->ObjectTypeMappers, 
                $this->ControlFlowMapper);
    }
}

?>