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
     * @param array[] $ParentRows The parent rows
     * @param array[] $RelatedRows The related rows
     * @return Map The map containing the parent rows mapped to an Array Object
     * containing their respective related rows
     */
    public function MapParentKeysToRelatedRows(array $ParentRows, array $RelatedRows);
    
    /**
     * Sync the supplied relationship changes by persisting to/discarding from the transaction
     * 
     * @param Transaction $Transaction The transaction to persist to
     * @param array $ParentData The parent result row
     * @param RelationshipChange[] $RelationshipChanges The mapped relationship changes
     * @return void
     */
    public function Persist(Transaction $Transaction, array $ParentData, array $RelationshipChanges);
}

?>