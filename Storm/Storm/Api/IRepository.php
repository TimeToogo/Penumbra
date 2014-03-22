<?php

namespace Storm\Api;

use \Storm\Pinq;

/**
 * The repository acts as a collection for set of entities
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRepository extends Pinq\IQueryable {
    
    public function GetEntityType();
    
    /**
     * @throws EntityNotFoundException
     */
    public function Get($_);
    public function GetOrNull($_);
    
    public function Add($Entity);
    public function AddAll(array $Entities);
    
    public function Remove($Entity);
    public function RemoveAll(array $Entities);
}

?>