<?php

namespace PenumbraExamples\One\Relational\Tables;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Constant\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\Traits;
use \Penumbra\Drivers\Base\Relational\Relations;

class Posts extends Relational\Table {
    protected function Name() {
        return 'Posts';
    }

    public $BlogId;
    public $AuthorId;
    public $Title;
    public $Content;
    public $CreatedDate;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->BlogId = $Column->IncrementInt32('BlogId');
        $this->AuthorId = $Column->Int32('AuthorId');
        $this->Title = $Column->String('Title', 50, true);
        $this->Content = $Column->String('Content', 2000);
        $this->CreatedDate = $Column->DateTime('CreatedDate');
    }
    
    public $BlogForeignKey;
    public $AuthorForeignKey;

    protected function CreateRelationalStructure(Database $Database) {
        $this->BlogForeignKey = new Traits\ForeignKey('BlogForeignKey', 
                Map::From([$this->BlogId], [$Database->Blogs->Id]),
                Traits\ForeignKeyMode::Cascade, Traits\ForeignKeyMode::Cascade);
        
        $this->AuthorForeignKey = new Traits\ForeignKey('AuthorForeignKey', 
                Map::From([$this->AuthorId], [$Database->Authors->Id]),
                Traits\ForeignKeyMode::Cascade, Traits\ForeignKeyMode::Cascade);
    }
    
    public $Blog;
    public $Author;
    public $Tags;
    
    protected function CreateRelations(Database $Database) {
        $this->Blog = new Relations\ToOneRelation($this->BlogForeignKey);
        $this->Author = new Relations\ToOneRelation($this->AuthorForeignKey);
        $this->Tags = new Relations\JoinTableRelation($Database->PostTags);
    }
}

?>
