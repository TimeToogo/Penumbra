<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog;

use \Storm\Tests\Integration\ORMTestCase;
use \Storm\Api;
use \Storm\Drivers\Base\Object\Properties\Proxies;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\IPlatform;
use \Storm\Drivers\Platforms;

class BlogTestCase extends ORMTestCase {
    protected function GetDomainDatabaseMapFactory(IPlatform $Platform) {
        return Mapping\BloggingDomainDatabaseMap::Factory($Platform);
    }
    
    protected function setUp() {
        $Connection = self::GetConnection();
        $SQL = <<<'SQL'
DROP TABLE IF EXISTS `authors`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `posttags`;
DROP TABLE IF EXISTS `blogs`;


CREATE TABLE `authors` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) 

CREATE TABLE `blogs` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  `Description` varchar(200) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) 

CREATE TABLE `posts` (
  `BlogId` int(11) NOT NULL AUTO_INCREMENT,
  `AuthorId` int(11) DEFAULT NULL,
  `Title` varchar(50) NOT NULL,
  `Content` varchar(2000) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`BlogId`,`Title`),
  KEY `AuthorForeignKey` (`AuthorId`),
  CONSTRAINT `AuthorForeignKey` FOREIGN KEY (`AuthorId`) REFERENCES `authors` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `BlogForeignKey` FOREIGN KEY (`BlogId`) REFERENCES `blogs` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) 

CREATE TABLE `tags` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Number` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) 

CREATE TABLE `posttags` (
  `PostBlogId` int(11) NOT NULL,
  `PostTitle` varchar(50) NOT NULL,
  `TagId` int(11) NOT NULL,
  PRIMARY KEY (`PostBlogId`,`PostTitle`,`TagId`),
  KEY `PostTags_Tags` (`TagId`),
  CONSTRAINT `PostTags_Posts` FOREIGN KEY (`PostBlogId`, `PostTitle`) REFERENCES `posts` (`BlogId`, `Title`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `PostTags_Tags` FOREIGN KEY (`TagId`) REFERENCES `tags` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
)
SQL;
        
        $Connection->Execute($SQL);
    }
    
    public function testEntitiesArePersistedAndRetrievedCorrectlyCorrectly() {
        $Blog = $this->CreateBlog(3, 5);
        $this->PersistBlog($Blog);
        
        $Connection = self::GetConnection();
        $Connection->Execute('');
    }
    
    private function PersistBlog(Entities\Blog $Blog) {
        $TagRepository = $this->GetRepository(Entities\Tag::GetType());
        $AuthorRepository = $this->GetRepository(Entities\Author::GetType());
        foreach ($Blog->Posts as $Post) {
            $TagRepository->PersistAll($Post->Tags->getArrayCopy());
            $AuthorRepository->Persist($Post->Author);
        }
        $this->GetStorm()->SaveChanges();
        
        $BlogRepository = $this->GetRepository(Entities\Blog::GetType());
        $BlogRepository->Persist($Blog);
        $BlogRepository->SaveChanges();
    }
    
    private function CreateBlog($AmountOfPosts, $AmountOfPostTags) {
        $Blog = new Entities\Blog();
        
        $Blog->Name = $this->Random(['Test blog', 'Worded blog', 'Someone\'s Blog', 'Read this now!']);
        $Blog->Description = $this->Random(['Another blog', 'Foo blog', 'Another time', 'What are doin\'?']);
        $Blog->CreatedDate = new \DateTime();
        
        $Blog->Posts = new \ArrayObject([]);
        while($AmountOfPosts > 0) {
            $Blog->Posts[] = $this->CreatePost($AmountOfPostTags);
            $AmountOfPosts--;
        }
        
        return $Blog;
    }

    private function CreateAuthor() {
        $Author = new Entities\Author();
        $Author->FirstName = $this->Random(['Joe', 'Jack', 'Bill', 'Tom', 'Sandy', 'Mat']);
        $Author->LastName = $this->Random(['Runt', 'Paffy', 'Derka', 'Shammy', 'Tuple', 'White']);
        
        return $Author;
    }

    private function CreatePosts(Entities\Blog $Blog, $AmountOfPostTags) {
        $Post1 = new Entities\Post();
        
        $Post1->Blog = $Blog;
        $Post1->Author = $this->CreateAuthor();
        $Post1->Title = $this->Random(['Foo', 'Bar', 'Hello World', 'Strikes back', 'Tomatoes are bad']);
        $Post1->Content = $this->Random(['Tasty cheese', 'The sky is exploding', 'Sunday\'s aren\'t as good as saturdays.', 'It is true!?']);
        $Post1->CreatedDate = new \DateTime();
        $Post1->Tags = new \ArrayObject([]);
        $this->AddTags($Post1, $AmountOfPostTags);
    }

    public function AddTags(Entities\Post $Post, $AmountOfTags) {
        while($AmountOfTags > 0) {
            $Tag = new Entities\Tag();
            $Tag->Name = $this->Random(['Tagged', 'Tummy', 'Tailgater', 'Food Fight', 'Andy']);
            $Tag->Description = 'This is a description - ' . $AmountOfTags;
            $Tag->Number = $Count * $Count;
            $Post->Tags[] = $Tag;
            $AmountOfTags--;
        }
    }
    
    private function Random(array $Values) {
        return $Values[rand(0, count($Values) - 1)];
    }
}

?>