<?php

namespace Penumbra\Core\Relational;

/**
 * This interface represents a relation in which a parent row can relate to one or no rows.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IToOneRelation extends IRelation {
    const IToOneRelationType = __CLASS__;
    
    /**
     * Map the parent rows to their respective related rows
     * 
     * @param ResultRow[] $ParentRows The parent rows
     * @param ResultRow[] $RelatedRows The related rows
     * @return ResultRow[] The array containing the parent rows indexed with their parents key
     */
    public function MapParentKeysToRelatedRow(array $ParentRows, array $RelatedRows);
    
    /**
     * Sync the supplied relationship change by persisting/discarding from the transaction
     * 
     * @param Transaction $Transaction The transaction to persist to
     * @param ResultRow $ParentData The parent row data
     * @param PrimaryKey|null $DiscardedPrimaryKey The primary key of the old related row
     * @param ColumnData|null $PersistedRelatedData The persisted data of the new related row
     * @return void
     */
    public function Persist(
            Transaction $Transaction, 
            ResultRow $ParentData, 
            PrimaryKey $DiscardedPrimaryKey = null, 
            ColumnData $PersistedRelatedData = null);
}

?>