<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Relational;

use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Base\Relational\IPlatform;

class BloggingDatabase extends Relational\Database {
    public function __construct(IPlatform $Platform) {
        parent::__construct($Platform);
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
