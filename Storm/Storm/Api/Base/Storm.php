<?php

namespace Storm\Api\Base;

use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Fluent\Object\Closure;

/**
 * The Storm class provides the api surrounding a DomainDatabaseMap.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Storm {
    /**
     * The supplied DomainDatabaseMap.
     * 
     * @var DomainDatabaseMap
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var ClosureToASTConverter 
     */
    protected $ClosureToASTConverter;
    
    public function __construct(
            DomainDatabaseMap $DomainDatabaseMap
            /*Closure\IReader $ClosureReader, 
            Closure\IParser $ClosureParser*/) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->ClosureToASTConverter = new ClosureToASTConverter(new Closure\Implementation\File\Reader(), new Closure\Implementation\PHPParser\Parser()); //$this->GetClosureToASTConverter($ClosureReader, $ClosureParser);
    }
    
    protected function GetClosureToASTConverter(
            Closure\IReader $ClosureReader, 
            Closure\IParser $ClosureParser) {
        return new ClosureToASTConverter($ClosureReader, $ClosureParser);
    }
    
    /**
     * @return DomainDatabaseMap 
     */
    final public function GetDomainDatabaseMap() {
        return $this->DomainDatabaseMap;
    }
    
    /**
     * Builds a new repository instance for a type of entity.
     * 
     * @param string|object $EntityType The entity of which the repository represents
     * @return Repository
     */
    public function GetRepository($EntityType, $AutoSave = false) {
        if(is_object($EntityType)) {
            $EntityType = get_class($EntityType);
        }
        
        return $this->ConstructRepository($EntityType, $AutoSave);
    }
    
    /**
     * Instantiates a new repository.
     * 
     * @return Repository The instantiated repository
     */
    protected function ConstructRepository($EntityType, $AutoSave = false) {
        return new Repository($this->DomainDatabaseMap, $this->ClosureToASTConverter, $EntityType, $AutoSave);
    }
}

?>
