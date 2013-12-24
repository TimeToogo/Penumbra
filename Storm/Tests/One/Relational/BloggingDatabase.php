<?php

namespace StormTests\One\Relational;

use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Platforms;
use \Storm\Drivers\Platforms\Development\Logging;

class BloggingDatabase extends Relational\Database {
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

    protected function Platform() {
        $Development = 0;
        
        if($Development > 0) {
            return new Platforms\Mysql\Platform(
                    new Logging\Connection(new Logging\DumpLogger(), 
                            new Platforms\PDO\Connection(
                                    new \PDO('mysql:host=localhost;dbname=StormTest', 'root', 'admin'))), 
                    $Development > 1);
        }
        else {
            return new Platforms\Mysql\Platform(
                            new Platforms\PDO\Connection(
                                    new \PDO('mysql:host=localhost;dbname=StormTest', 'root', 'admin')), 
                    false);
        }
    }
}

?>
