<?php

namespace StormExamples\One\Relational;

use \Storm\Drivers\Constant\Relational;

class BloggingDatabase extends Relational\Database {
        
    public $Blogs;
    public $Authors;
    public $Profiles;
    public $Posts;
    public $Tags;
    public $PostTags;
    public $AuthorFriends;
    
    protected function CreateTables() {
        $this->Blogs = new Tables\Blogs();
        $this->Authors = new Tables\Authors();
        $this->Profiles = new Tables\Profiles();
        $this->Posts = new Tables\Posts();
        $this->Tags = new Tables\Tags();
        $this->PostTags = new Tables\PostTags();
        $this->AuthorFriends = new Tables\AuthorFriends();
    }
}

?>
