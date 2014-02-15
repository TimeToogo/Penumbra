<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Fluent\Object\Functional;

class Criterion extends Object\Criterion {
    
    public function __construct($EntityType) {
        parent::__construct($EntityType);
    }
    
    public function AddPredicateClosure(Functional\IAST $PredicateAST) {
        $this->AddPredicate($this->ParseReturnExpression($PredicateAST));
    }
    
    public function AddOrderByClosure(Functional\IAST $OrderByAST, $Ascending) {
        $this->AddOrderByExpression($this->ParseReturnExpression($OrderByAST), $Ascending);
    }
    
    public function AddGroupByClosure(Functional\IAST $GroupByAST) {
        $this->AddGroupByExpression($this->ParseReturnExpression($GroupByAST));
    }
    
    private function ParseReturnExpression(Functional\IAST $AST) {
        if(!$AST->HasEntityMap() || $AST->GetEntityMap()->GetEntityType() !== $this->GetEntityType()) {
            throw new \Storm\Core\Object\TypeMismatchException(
                    'The supplied AST must be of entity type %s: %s given',
                    $this->GetEntityType(),
                    $AST->HasEntityMap() ?  $AST->GetEntityMap()->GetEntityType() : 'null');
        }
        if(!$AST->HasReturnNode()) {
            throw new FluentException(
                    'The supplied closure must contain a valid return statement');
        }
        $ReturnNodes = $AST->GetReturnNodes();
        if(count($ReturnNodes) > 1) {
            throw new FluentException(
                    'The supplied closure must contain a single return statement');
        }
        
        $AST->SetPropertyMode(Functional\IAST::PropertiesAreGetters);
        
        return $AST->ParseNode($ReturnNodes[0]);
    }
}

?>
