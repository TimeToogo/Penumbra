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
     * Gets the related table.
     * 
     * @return Table
     */
    public function GetTable();
    
    /**
     * @return int
     */
    public function GetPersistingDependencyOrder();
    
    /**
     * @return int
     */
    public function GetDiscardingDependencyOrder();
    
    /**
     * Constructs a request that is constrained by this relation.
     * If parent rows are specified the request will be constrained such
     * that it only loads the related rows of the parents.
     * 
     * @parent ResultRows[]|null $ParentRows The parent rows
     * @return Request
     */
    public function RelationRequest(array $ParentRows = null);
    
    /**
     * Adds the relation constraint to the supplied request.
     * If parent rows are specified the request will be constrained such
     * that it only loads the related rows of the parents.
     * 
     * @parent ResultRows[]|null $ParentRows The parent rows
     * @return void
     */
    public function AddRelationToRequest(Request $Request, array $ParentRows = null);
}

?>