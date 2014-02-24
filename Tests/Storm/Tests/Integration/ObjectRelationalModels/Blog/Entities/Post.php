<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Entities;

class Post extends Entity {
    public $Blog;
    public $Author;
    public $Title;
    public $Content;
    public $CreatedDate;
    public $Tags;
}

?>
