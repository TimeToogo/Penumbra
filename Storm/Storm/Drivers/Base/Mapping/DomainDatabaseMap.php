<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping;
use \Storm\Core\Object\Expressions\Expression;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $ExpressionMapper;
    
    public function __construct() {
        parent::__construct();
        
        $this->ExpressionMapper = new ExpressionMapper($this->GetDatabase()->GetPlatform());
    }
    
    final protected function MapExpression(Mapping\IEntityRelationalMap $EntityRelationalMap, \Storm\Core\Object\Expressions\Expression $Expression) {
        return $this->ExpressionMapper->Map($EntityRelationalMap, $Expression);
    }
}

?>