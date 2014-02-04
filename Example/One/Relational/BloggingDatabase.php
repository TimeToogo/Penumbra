<?php

namespace StormExamples\One\Relational;

use \Storm\Drivers\Constant\Relational;

class BloggingDatabase extends Relational\Database {
    public function __construct() {
        parent::__construct(\StormExamples\One\One::GetPlatform());
    }
    
    public $Blogs;
    public $Authors;
    public $Posts;
    public $Tags;
    public $PostTags;
    
    protected function CreateTables() {
        $this->Blogs = new Tables\Blogs();
        $this->Authors = new Tables\Authors();
        $this->Posts = new Tables\Posts();
        $this->Tags = new Tables\Tags();
        $this->PostTags = new Tables\PostTags();
    }
}

?>
