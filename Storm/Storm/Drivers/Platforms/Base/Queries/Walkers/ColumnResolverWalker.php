<?php

namespace Storm\Drivers\Platforms\Base\Queries\Walkers;

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
            return R\Expression::Identifier([$SourceAlias, $Expression->GetColumn()->GetName()]);
        }
        
        return parent::WalkColumn($Expression);
    }
}

?>