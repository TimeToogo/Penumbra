<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\Domain;
use \Storm\Core\Object\RevivalData;

trait EntityProxyFunctionality {
    private $__Domain;
    private $__IsConstructed = false;
    private $__IsCloning = false;
    private $__IsLoading = false;
    private $__IsLoaded = false;
    private $__OriginalEntity = null;
    private $__UnsetQueue = array();
    private $__LoadRevivalDataFunction;
    
    protected static $__Reflection;
    protected static $__ClassName;
    protected static $__EntityClassName;
    protected static $__EntityReflection;
    protected static $__EntityProperties = array();
    protected static $__EntityMethods = array();
    protected static $__IsInitialized = false;
    
    private function __ConstructProxy(Domain $Domain, callable $LoadRevivalDataFunction) {
        $this->Initialize();
        $this->__Domain = $Domain;
        $this->__LoadRevivalDataFunction = $LoadRevivalDataFunction;
        foreach(static::$__EntityProperties as $Property) {
            if($Property->getDeclaringClass()->getName() !== static::$__ClassName
                    && $Property->isPublic() && !$Property->isStatic()) {
                unset($this->{$Property->getName()});
            }
        }
        $this->__IsConstructed = true;
    }
    private function Initialize() {
        if(static::$__IsInitialized) {
            return;
        }
        
        static::$__Reflection = new \ReflectionClass($this);
        static::$__ClassName = static::$__Reflection->getName();
        static::$__EntityClassName = static::$__Reflection->getParentClass()->getName();
        
        static::$__EntityReflection = new \ReflectionClass(get_parent_class());    
        foreach(static::$__EntityReflection->getProperties() as $Property) {
            static::$__EntityProperties[$Property->getName()] = $Property;
        }
        foreach(static::$__EntityReflection->getMethods() as $Method) {
            static::$__EntityMethods[$Method->getName()] = $Method;
        }
        
        static::$__IsInitialized = true;
    }
    
    final public function __IsLoaded() {
        return $this->__IsLoaded;
    }
    
    final public function __IsAltered() {
        if(!$this->__IsLoaded) {
            return false;
        }
        return $this->__OriginalEntity != $this;
    }
    
    private function __GetStateHash() {
        return md5(serialize($this));
    }
    
    private function __ShouldNotLoad() {
        return $this->__IsLoading || $this->__IsLoaded || !$this->__IsConstructed || $this->__IsCloning;
    }
    
    private function __Load() {
        if($this->__ShouldNotLoad()) {
            return;
        }
        $this->__IsLoading = true;
        
        $LoadFunction = $this->__LoadRevivalDataFunction;
        $RevivalData = $LoadFunction();
        $this->__Revive($RevivalData);
        $this->__OriginalEntity = clone $this;
        
        unset($this->__LoadEntityFunction);
        unset($this->__Domain);
        foreach($this->__UnsetQueue as $Variable) {
            if(property_exists($this, $Variable)) {
                unset($this->$Variable);
            }
        }
        
        $this->__IsLoading = false;
        $this->__IsLoaded = true;
    }
    
    private function __Revive(RevivalData $RevivalData) {
        $this->__Domain->LoadEntity($RevivalData, $this);
    }
    
    private function HasParentMethod($Method) {
        return isset(static::$__EntityMethods[$Method]);
    }
    private function FowardMethodCall($Method, array $Parameters) {
        return static::$__EntityMethods[$Method]->invokeArgs($this, $Parameters);
    }
    
    public function __call($Name, $Arguments) {
        if($this->HasParentMethod(__FUNCTION__)) {
            $this->__Load();
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
        }
        else
            trigger_error(sprintf('Call to undefined method %s::%s()', static::$__EntityClassName, $Name), E_USER_ERROR);
    }

    public function __get($Name) {
        $this->__Load();
        if($this->HasParentMethod(__FUNCTION__))
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
        else if(isset($this->$Name) || property_exists($this, $Name))
            return $this->$Name;
        else
            trigger_error(sprintf('Undefined property: %s::$%s', static::$__EntityClassName, $Name), E_USER_NOTICE);
    }

    public function __invoke() {
        if($this->HasParentMethod(__FUNCTION__)) {
            $this->__Load();
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
        }
        else
            trigger_error('Function name must be a string', E_USER_ERROR);
    }

    public function __set($Name, $Value) {
        $this->__Load();
        if($this->HasParentMethod(__FUNCTION__))
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
        else
            $this->$Name = $Value;
    }
    
    public function __isset($Name) {
        $this->__Load();
        if($this->HasParentMethod(__FUNCTION__))
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
        else
            return isset($this->$Name);
    }

    public function __unset($Name) {
        if($this->HasParentMethod(__FUNCTION__)) {
            $this->__Load();
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
        }
        else if($this->__ShouldNotLoad()) {
            $this->__UnsetQueue[] = $Name;
        }
        else {
            unset($this->$Name);
        }
    }
    
    public function __CloneProxyInstance() {
        $this->__IsCloning = true;
        $ClonedProxy = clone $this;
        $this->__IsCloning = false;
        
        return $ClonedProxy;
    }
    
    public function __clone() {
        $this->__Load();
        if($this->HasParentMethod(__FUNCTION__))
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
    }

    public function __destruct() {
        if($this->HasParentMethod(__FUNCTION__))
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
    }
    
    public function __sleep() {
        $this->__Load();
        if($this->HasParentMethod(__FUNCTION__))
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
    }
    public function __wakeup() {
        $this->Initialize();
        if($this->HasParentMethod(__FUNCTION__))
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
    }
    
    public function __toString() {
        if($this->HasParentMethod(__FUNCTION__)) {
            $this->__Load();
            return $this->FowardMethodCall(__FUNCTION__, func_get_args());
        }
        else
            trigger_error(sprintf('Object of class %s could not be converted to string', static::$__EntityClassName), E_RECOVERABLE_ERROR);
    }
}

?>