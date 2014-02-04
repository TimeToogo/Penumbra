<?php

namespace StormExamples\One\Relational\Tables;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational\Database;
use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Storm\Drivers\Platforms\Mysql;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;
use \Storm\Drivers\Base\Relational\Traits;
use \Storm\Drivers\Base\Relational\Relations;

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
