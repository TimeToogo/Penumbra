<?php

namespace Storm\Core\Relational;

final class DependencyOrder {
    const Before = -1;
    const After = 1;
}

final class DependencyMode {
    const Persisting = 1;
    const Discarding = -1;
}

/**
 * This interface represents a relation between two tables.
 * NOTE: The parent cardinality does not matter as retreiving related rows
 * is always does in the context of many parent rows while persisting relating
 * rows is always done in the context of one parent row.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRelation {
    /**
     * Gets the parent table.
     * 
     * @return ITable
     */
    public function GetParentTable();
    
    /**
     * Gets the related table.
     * 
     * @return ITable
     */
    public function GetRelatedTable();
    
    /**
     * @return int
     */
    public function GetPersistingDependencyOrder();
    
    /**
     * @return int
     */
    public function GetDiscardingDependencyOrder();
    
    /**
     * Constructs a select that is constrained by this relation.
     * If parent rows are specified the request will be constrained such
     * that it only loads the related rows of the parents.
     * 
     * @param ResultRows[]|null $ParentRows The parent rows
     * @return ResultSetSelect
     */
    public function RelationResultSetSelect(array $ParentRows = null);
    
    /**
     * Gets the columns required by the relation to the parent request.
     * 
     * @return void
     */
    public function GetParentColumns();
    
    /**
     * Gets the columns referenced columns.
     * 
     * @return void
     */
    public function GetReferencedColumns();
    
    /**
     * Adds the relation constraint to the supplied result set specification.
     * If parent rows are specified the request will be constrained such
     * that it only loads the related rows of the parents.
     * 
     * @param ResultSetSpecification $ResultSetSpecification
     * @param ResultRows[]|null $ParentRows The parent rows
     * @return void
     */
    public function AddRelationToResultSet(ResultSetSpecification $ResultSetSpecification, array $ParentRows = null);
    
    /**
     * Map the relational data (foreign key data) from the parent row to the related row.
     * 
     * @param ResultRow $ParentRow The parent row
     * @param ResultRow $RelatedRow The related row
     * @return void
     */
    public function MapRelationalParentDataToRelatedData(ColumnData $ParentRow, ColumnData $RelatedRow);
}

?>