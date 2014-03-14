<?php

namespace Storm\Api\Base;

use \Storm\Api\IConfiguration;
use \Storm\Core\Object;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base;
use \Storm\Pinq;
use \Storm\Pinq\Functional;

/**
 * The repository acts as a simple collection for 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRepository extends Pinq\IQueryable {
    
    public function GetEntityType();
    
    public function Get($_);
    public function GetOrDefault($_, $Default = null);
    
    public function Add($Entity);
    public function AddAll(array $Entities);
    
    public function Remove($Entity);
    public function RemoveAll(array $Entities);
}

?>