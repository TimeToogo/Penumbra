<?php

namespace StormTests\One\Relational\Tables;

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

    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return new \Storm\Drivers\Platforms\Null\NullKeyGenerator();
    }
    
    public $BlogId;
    public $Title;
    public $Content;
    public $CreatedDate;
    
    public $PrimaryKey;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->BlogId = new Column('BlogId', new Mysql\Columns\DataTypes\HexedBinaryDataType(16));
        $this->Title = new Column('Title', new DataType('VARCHAR', [50]));
        $this->Content = new Column('Content', new DataType('VARCHAR', [2000]));
        $this->CreatedDate = new Column('CreatedDate', new Mysql\Columns\DataTypes\DateTimeDataType());
        
        $this->PrimaryKey = new Traits\PrimaryKey([$this->BlogId, $this->Title]);
    }
    
    public $BlogForeignKey;

    protected function CreateRelationalStructure(Database $Database) {
        $this->BlogForeignKey = new Traits\ForeignKey('BlogForeignKey', $Database->Blogs, 
                Map::From([$this->BlogId], [$Database->Blogs->Id]),
                Traits\ForeignKeyMode::Cascade, Traits\ForeignKeyMode::Cascade);
    }
    
    public $Blog;
    public $Tags;
    
    protected function CreateRelations(Database $Database) {
        $this->Blog = new Relations\ToOneRelation($this->BlogForeignKey);
        $this->Tags = new Relations\JoinTableRelation($Database->PostTags);
    }
}

?>
