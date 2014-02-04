<?php

namespace Storm\Drivers\Base\Object\Properties\Collections;

use \Storm\Core\Object;
use \Storm\Core\Object\Domain;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;

class LazyCollection extends Collection {
    private $Domain;
    /**
     * @var IProxyGenerator|null
     */
    private $ProxyGenerator;
    private $AlreadyKnownRevivalData;
    private $ArrayLoaderFunction;
    private $IsLoaded = false;
    
    public function __construct(
            Domain $Domain, 
            $EntityType, 
            Object\RevivalData $AlreadyKnownRevivalData,
            callable $ArrayLoaderFunction,
            IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($EntityType, array());
        $this->AlreadyKnownRevivalData = $AlreadyKnownRevivalData;
        $this->ArrayLoaderFunction = $ArrayLoaderFunction;
        $this->Domain = $Domain;
        $this->ProxyGenerator = $ProxyGenerator;
    }
    private function Load() {
        if($this->IsLoaded) {
            return;
        }
        else {
            $this->IsLoaded = true;
        }
        
        $Loader = $this->ArrayLoaderFunction;
        $RevivalData = $Loader();
        $this->exchangeArray($this->LoadEntities($RevivalData));
        $this->SetIsAltered(false);
        $this->OriginalEntities = $this->LoadEntities($RevivalData);
    }
    private function LoadEntities(array $RevivalData) {
        if($this->ProxyGenerator !== null) {
            $LoaderFunctions = array_map(
                    function (Object\RevivalData $RevivalData) {
                        return function () use ($RevivalData) {
                            return $RevivalData;
                        };
                    }, 
                    $RevivalData);
            
            return $this->ProxyGenerator->GenerateProxies(
                    $this->Domain, 
                    $this->GetEntityType(),
                    array_fill_keys(array_keys($LoaderFunctions), $this->AlreadyKnownRevivalData),
                    $LoaderFunctions);
        }
        else {
            return $this->Domain->ReviveEntities($this->GetEntityType(), $RevivalData);
        }
    }
    
    final public function __IsLoaded() {
        return $this->IsLoaded;
    }
    
    public function count() {
        $this->Load();
        return parent::count();
    }
    
    public function ToArray() {
        $this->Load();
        return parent::ToArray();
    }

    public function getIterator() {
        $this->Load();
        return parent::getIterator();
    }

    public function offsetExists($index) {
        $this->Load();
        return parent::offsetExists($index);
    }

    public function offsetGet($index) {
        $this->Load();
        return parent::offsetGet($index);
    }

    public function offsetSet($index, $newval) {
        $this->Load();
        return parent::offsetSet($index, $newval);
    }

    public function offsetUnset($index) {
        $this->Load();
        return parent::offsetUnset($index);
    }

}

?>
