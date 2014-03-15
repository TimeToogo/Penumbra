<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class StandardResultSetSourceCompiler implements IResultSetSourceCompiler {
    public function AppendResultSetSources(
            QueryBuilder $QueryBuilder, 
            Relational\ResultSetSources $Sources,
            \SplObjectStorage $SourceAliasMap) {
        $FirstSource = $Sources->GetSource();
        $this->AppendSource($QueryBuilder, $FirstSource, $SourceAliasMap[$FirstSource]);
        
        if($Sources->IsJoined()) {
            foreach($QueryBuilder->Delimit($Sources->GetJoins(), ' ') as $Join) {
                $QueryBuilder->Append($this->GetJoinType($Join->GetJoinType()) . ' ');
                
                $Source = $Join->GetSource();
                $this->AppendSource($QueryBuilder, $Source, $SourceAliasMap[$Source]);
                
                $QueryBuilder->Append(' ON ');
                
                $QueryBuilder->AppendExpression($Join->GetJoinPredicateExpression());
            }
        }
    }
    
    private function AppendSource(QueryBuilder $QueryBuilder, Relational\IResultSetSource $Source, $Alias) {
        if($Source instanceof Relational\ITable) {
            $QueryBuilder->AppendIdentifier('#', $Source->GetName());
        }
        else if ($Source instanceof Relational\ResultSetSelect) {
            $QueryBuilder->Append('(');
            $QueryBuilder->AppendSelect($Source);
            $QueryBuilder->AppendIdentifier(')');
        }
        else {
            throw new Relational\RelationalException(
                    'Unknown result set source type: %s',
                    get_class($Source));
        }
        
        $this->AppendAlias($QueryBuilder, $Alias);
    }
    
    protected function AppendAlias(QueryBuilder $QueryBuilder, $Alias) {
        $QueryBuilder->AppendIdentifier('AS #', $Alias);
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
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied join type: %s', 
                    get_class($this),
                    $JoinType);
        }
    }
}

?>