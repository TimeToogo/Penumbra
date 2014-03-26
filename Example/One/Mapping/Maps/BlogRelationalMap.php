<?php

namespace PenumbraExamples\One\Mapping\Maps;

use \Penumbra\Drivers\Constant\Mapping;
use \PenumbraExamples\One\Entities\Blog;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

final class BlogRelationalMap extends Mapping\EntityRelationalMap {
    
    protected function EntityMap(Object\Domain $Domain) {
        return $Domain->GetEntityMap(Blog::GetType());
    }
    
    protected function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database) {
        /* @var $EntityMap \PenumbraExamples\One\Domain\Maps\BlogMap */
        /* @var $Table \PenumbraExamples\One\Relational\Tables\Blogs */
        $Table = $Database->Blogs;
        
        $this->Map($EntityMap->Id)->ToColumn($Table->Id);
        $this->Map($EntityMap->Name)->ToColumn($Table->Name);
        $this->Map($EntityMap->Description)->ToColumn($Table->Description);
        $this->Map($EntityMap->CreatedDate)->ToColumn($Table->CreatedDate);
        
        $this->Map($EntityMap->Posts)->ToCollection($Table->Posts);
    }
    
    protected function SelectSources(Relational\ResultSetSources $SelectSources, Relational\Database $Database) {
        //$SelectSources->AddJoin(new Relational\Join(Relational\JoinType::Left, $Database->Authors));
    }
}

?>