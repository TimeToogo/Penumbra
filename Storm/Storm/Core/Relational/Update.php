<?php

namespace Storm\Core\Relational;

use \Storm\Core\Relational\Expressions;

/**
 * The update represents a set of changes to columns values to
 * a variable amount of rows defined by a criteria.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Update extends Query {
    /**
     * @var IColumn[]
     */
    private $Columns = [];
    
    /**
     * @var \SplObjectStorage<IColumn, Expression>
     */
    private $ColumnExpressionMap;
    
    public function __construct(ResultSetSpecification $ResultSetSpecification) {
        parent::__construct($ResultSetSpecification);
        $this->ColumnExpressionMap = new \SplObjectStorage();
    }
    
    /**
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return \SplObjectStorage<IColumn, Expression>
     */
    final public function GetColumnExpressionMap() {
        return $this->ColumnExpressionMap;
    }
    
    final public function AddColumn(IColumn $Column, Expression $NewValueExpression) {
        if(!$this->Sources->ColumnHasSource($Column)) {
            throw new RelationalException(
                    'The supplied column cannot be added to the update: %s is not part of the result set source',
                    $Column->GetName());
        }
        
        $this->Columns[] = $Column;
        $this->ColumnExpressionMap[$Column] = $NewValueExpression;
    }
}

?>