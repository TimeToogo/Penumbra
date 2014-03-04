<?php

namespace StormExamples\One\Mapping\Maps;

use \Storm\Drivers\Constant\Mapping;
use \StormExamples\One\Entities\Blog;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

final class BlogRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Blog::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \StormExamples\One\Domain\Maps\BlogMap */
        /* @var $Table \StormExamples\One\Relational\Tables\Blogs */
        $Table = $Database->Blogs;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        $this->Map($EntityMap->Posts)->ToCollection($Table->Posts);
    }
    
    protected function RelationalCriterion(Relational\Criterion $RelationalCriterion, Relational\Database $Database) {
        $RelationalCriterion->AddJoin(new Relational\Join(Relational\JoinType::Left, $Database->Authors));
    }
}

?>