<?php

namespace Storm\Core\Relational;

/**
 * This interface represents a relation in which a parent row can relate to a variable
 * amount of related rows.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IToManyRelation extends IRelation {
    const IToManyRelationType = __CLASS__;
    
    /**
     * Map the parent rows to their respective related rows
     * 
     * @param ResultRow[] $ParentRows The parent rows
     * @param ResultRow[] $RelatedRows The related rows
     * @return ResultRow[] The array containing the parent rows indexed by their parents key
     */
    public function MapParentKeysToRelatedRows(array $ParentRows, array $RelatedRows);
    
    /**
     * Sync the supplied relationship changes by persisting to/discarding from the transaction
     * 
     * @param Transaction $Transaction The transaction to persist to
     * @param ResultRow $ParentData The parent result row
     * @param RelationshipChange[] $RelationshipChanges The mapped relationship changes
     * @return void
     */
    public function Persist(Transaction $Transaction, ResultRow $ParentData, array $RelationshipChanges);
}

?>