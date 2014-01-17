<?php

namespace StormTests\One\Relational;

use \Storm\Drivers\Constant\Relational;

class BloggingDatabase extends Relational\Database {
    public function __construct() {
        parent::__construct(static::InitializePlatform());
    }
    
    public static function InitializePlatform() {
        return \StormTests\One\Test::GetPlatform();
    }
    
    public $Blogs;
    public $Posts;
    public $Tags;
    public $PostTags;
    
    protected function CreateTables() {
        $this->Blogs = new Tables\Blogs();
        $this->Posts = new Tables\Posts();
        $this->Tags = new Tables\Tags();
        $this->PostTags = new Tables\PostTags();
    }
}

?>
