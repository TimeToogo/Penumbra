<?php

namespace Penumbra\Drivers\Platforms\Standard\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Platforms\Base\Queries\IResultSetSourceCompiler;
use \Penumbra\Drivers\Platforms\Base\Queries\ColumnResolverWalker;

class ResultSetSourceCompiler implements IResultSetSourceCompiler {
    
    public function __construct() {
        $this->JoinTypes = $this->JoinTypes();
    }
    
    public function AppendResultSetSources(
            QueryBuilder $QueryBuilder, 
            Relational\ResultSetSources $Sources,
            ColumnResolverWalker $ColumnResolverWalker) {
        $FirstSource = $Sources->GetSource();
        $SourceAliasMap = $ColumnResolverWalker->GetSourceAliasMap();
        $this->AppendSource($QueryBuilder, $FirstSource, $SourceAliasMap[$FirstSource]);
        
        if($Sources->IsJoined()) {
            foreach($QueryBuilder->Delimit($Sources->GetJoins(), ' ') as $Join) {
                $QueryBuilder->Append($this->GetJoinType($Join->GetJoinType()) . ' ');
                
                $Source = $Join->GetSource();
                $this->AppendSource($QueryBuilder, $Source, $SourceAliasMap[$Source], $ColumnResolverWalker);
                
                $QueryBuilder->Append(' ON ');
                
                $QueryBuilder->AppendExpression($ColumnResolverWalker->Walk($Join->GetJoinPredicateExpression()));
            }
        }
    }
    
    private function AppendSource(QueryBuilder $QueryBuilder, Relational\IResultSetSource $Source, $Alias) {
        if($Source instanceof Relational\ITable) {
            $QueryBuilder->AppendIdentifier('#', [$Source->GetName()]);
        }
        else if ($Source instanceof Relational\ResultSetSelect) {
            $QueryBuilder->Append('(');
            $QueryBuilder->AppendSelect($Source);
            $QueryBuilder->Append(')');
        }
        else {
            throw new Relational\RelationalException(
                    'Unknown result set source type: %s',
                    get_class($Source));
        }
        
        $this->AppendAlias($QueryBuilder, $Alias);
    }
    
    protected function AppendAlias(QueryBuilder $QueryBuilder, $Alias) {
        $QueryBuilder->AppendIdentifier('AS #', [$Alias]);
    }
    
    private $JoinTypes;
    protected function JoinTypes() {
        return [
            Relational\JoinType::Inner => 'INNER JOIN',
            Relational\JoinType::Left => 'LEFT JOIN',
            Relational\JoinType::Right => 'RIGHT JOIN',
            Relational\JoinType::Full => 'FULL JOIN',
            Relational\JoinType::Cross => 'CROSS JOIN',
        ];
    }
    
    private function GetJoinType($JoinType) {
        if (isset($this->JoinTypes[$JoinType])) {
            return ' ' . $this->JoinTypes[$JoinType] . ' ';
        }
        else {
            throw new \Penumbra\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied join type: %s', 
                    get_class($this),
                    $JoinType);
        }
    }
}

?>