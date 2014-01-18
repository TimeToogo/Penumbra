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
     * @return Map The map containing the parent rows mapped to an Array Object
     * containing their respective related rows
     */
    public function MapParentToRelatedRows(array $ParentRows, array $RelatedRows);
    
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