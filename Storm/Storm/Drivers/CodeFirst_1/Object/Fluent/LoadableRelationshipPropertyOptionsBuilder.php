<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Relationships;
use \Storm\Drivers\Constant\Mapping\LoadingMode;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class LoadableRelationshipPropertyOptionsBuilder extends RelationshipPropertyOptionsBuilder {
        
    public function WithEagerLoading() {
        $this->Metadata->Add(new Metadata\LoadingMode(LoadingMode::Eager));
    }
    
    public function WithSemiLazyLoading() {
        $this->Metadata->Add(new Metadata\LoadingMode(LoadingMode::SemiLazy));
    }
    
    public function WithLazyLoading() {
        $this->Metadata->Add(new Metadata\LoadingMode(LoadingMode::Lazy));
    }
    
    public function WithExtraLazyLoading() {
        $this->Metadata->Add(new Metadata\LoadingMode(LoadingMode::ExtraLazy));
    }
}