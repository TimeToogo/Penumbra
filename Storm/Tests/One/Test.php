<?php

namespace StormTests\One;

use \StormTests\One\Entities;
use \Storm\Core\Storm;
use \Storm\Core\Repository;
use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Requests;
use \Storm\Drivers\Intelligent\Object\Pinq;
use \Storm\Drivers\Intelligent\Object\Pinq\Expressions\Expression;

class Test implements \StormTests\IStormTest {
    public function GetStorm() {
        return new Storm(new Mapping\BloggingDomainDatabaseMap());
    }
    
    const Persist = 0;
    const Retreive = 1;
    const Discard = 2;
    public function Run(Storm $BloggingStorm) {        
        $BlogRepository = $BloggingStorm->GetRepository(Entities\Blog::GetType());
        
        $Action = self::Persist;
        $Amount = 1;
        
        $Last;
        for ($Count = 0; $Count < $Amount; $Count++) {
            $Last = $this->Act($Action, $BloggingStorm, $BlogRepository);
        }
        
        //return $Last;
    }
    
    private function Act($Action, Storm $BloggingStorm, Repository $BlogRepository) {
        $Id = '91CFD8806B9B11E38E8100270E076073';
        if($Action === self::Persist) {
            $Blog = $this->CreateBlog();
            
            $BlogRepository->Persist($Blog);
            $BlogRepository->SaveChanges();
            
            return $Blog;
        }
        else if ($Action === self::Discard) {
            $BlogMap = $BloggingStorm->GetORM()->GetDomain()->GetEntityMap(Entities\Blog::GetType());
            
            $Request = new Pinq\Request($BlogMap);
            $Request->Where(function ($Blog) use(&$Id) { return $Blog->Id === $Id; });
            
            $BlogRepository->Discard($Request);
        }
        else if($Action === self::Retreive) {
            $BlogMap = $BloggingStorm->GetORM()->GetDomain()->GetEntityMap(Entities\Blog::GetType());
            
            static $Request = null;
            if($Request === null && 1==2) {
                $Request = new Pinq\Request($BlogMap, true);
                $Request->Where(function ($Blog) { 
                    return $Blog->Name === 'Test blog' || $Blog->CreatedDate < new \DateTime();
                });
            }
            $Identity = $BlogMap->Identity();
            $Identity->SetProperty('Id', $Id);
            
            
            $RevivedBlog = $BlogRepository->Load(new Requests\IdentityRequest($Identity));
            
            $RevivedBlog->Posts[0]->Tags->ToArray();
            $RevivedBlog->Posts[1]->Tags->ToArray();
            
            return null;
        }
    }
    
    private function CreateBlog() {
        $Blog = new Entities\Blog();
        $Blog->Name = 'Test blog';
        $Blog->Description = 'The tested blog';
        $Blog->CreatedDate = new \DateTime();
        $Blog->Posts = new \Storm\Drivers\Base\Mapping\Collections\Collection([], Entities\Post::GetType());
        $this->CreatePosts($Blog);
        
        return $Blog;
    }
    
    private function CreatePosts(Entities\Blog $Blog) {
        $Post1 = new Entities\Post();
        $Post1->Blog = $Blog;
        $Post1->Title = 'Hello World';
        $Post1->Content = 'What\'s up?';
        $Post1->CreatedDate = new \DateTime();
        $Post1->Tags = new \Storm\Drivers\Base\Mapping\Collections\Collection([] , Entities\Tag::GetType());
        $this->AddTags($Post1);
        $Blog->Posts[] = $Post1;
        
        $Post2 = new Entities\Post();
        $Post2->Blog = $Blog;
        $Post2->Title = 'Hello Neptune';
        $Post2->Content = 'What\'s going on nup?';
        $Post2->CreatedDate = new \DateTime();
        $Post2->Tags = new \Storm\Drivers\Base\Mapping\Collections\Collection([] , Entities\Tag::GetType());
        $this->AddTags($Post2);
        $Blog->Posts[] = $Post2;
    }
    
    public function AddTags(Entities\Post $Post) {
        $Names = ['Tagged', 'Tummy', 'Tailgater', 'Food Fight', 'Andy'];
        $Count = 100;
        while($Count > 0) {
            $Tag = new Entities\Tag();
            $Tag->Name = $Names[rand(0, count($Names) - 1)];
            $Post->Tags[] = $Tag;
            $Count--;
        }
    }
}

return new Test();

?>