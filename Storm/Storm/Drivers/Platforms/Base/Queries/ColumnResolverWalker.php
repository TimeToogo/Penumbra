<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions as R;

class ColumnResolverWalker extends R\ExpressionWalker {
    /**
     * @var \SplObjectStorage 
     */
    private $SourceAliasMap;
    
    public function __construct(\SplObjectStorage $SourceAliasMap) {
        $this->SourceAliasMap = $SourceAliasMap;
    }
    
    public function GetSourceAliasMap() {
        return $this->SourceAliasMap;
    }
        
    public function WalkColumn(R\ColumnExpression $Expression) {
        $Source = $Expression->GetSource();
        
        if(isset($this->SourceAliasMap[$Source])) {
            $SourceAlias = $this->SourceAliasMap[$Source];
            return R\EscapedValueExpression::Identifier([$SourceAlias, $this->GetColumnName($Expression)]);
        }
        
        return parent::WalkColumn($Expression);
    }
    
    private function GetColumnName(R\ColumnExpression $Expression) {
        $Column = $Expression->GetColumn();
        
        return $Expression->GetSource() instanceof Relational\ITable ? $Column->GetName() : $Column->GetIdentifier();
    }
}

?>