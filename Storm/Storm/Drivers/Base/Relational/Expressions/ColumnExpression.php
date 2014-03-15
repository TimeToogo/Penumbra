<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;

/**
 * Expression representing a column in a table.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ColumnExpression extends Expression {
    /**
     * @var Relational\IResultSetSource 
     */
    private $Source;
    
    /**
     * @var Relational\IColumn 
     */
    private $Column;
    
    public function __construct(Relational\IResultSetSource $Source, Relational\IColumn $Column) {
        if(!$Source->HasColumn($Column)) {
            throw new Relational\RelationalException(
                    'Cannot create column expression with source that does not contain the supplied column: %s',
                    $Column->GetName());
        }
        $this->Source = $Source;
        $this->Column = $Column;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkColumn($this);
    }
    
    /**
     * @return Relational\IResultSetSource
     */
    public function GetSource() {
        return $this->Source;
    }

    /**
     * @return Relational\IColumn
     */
    final public function GetColumn() {
        return $this->Column;
    }
    
    public function Update(Relational\IResultSetSource $Source, Relational\IColumn $Column) {
        if($this->Source === $Source
                && $this->Column === $Column) {
            return $this;
        }
        
        return new self($Source, $Column);
    }
}

?>