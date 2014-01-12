<?php

namespace StormTests\One;

use \StormTests\One\Entities;
use \Storm\Api;
use \Storm\Api\Base\Storm;
use \Storm\Api\Base\Repository;
use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Procedures;
use \Storm\Drivers\Base\Object\Requests;
use \Storm\Drivers\Intelligent\Object\Pinq;
use \Storm\Drivers\Platforms;
use \Storm\Drivers\Platforms\Development\Logging;

class Test implements \StormTests\IStormTest {
    
    public static function GetPlatform() {
        $Development = 1;
        
        if($Development > 0) {
            return new Platforms\Mysql\Platform(
                    new Logging\Connection(new Logging\DumpLogger(), 
                            new Platforms\PDO\Connection(
                                    new \PDO('mysql:host=localhost;dbname=StormTest', 'root', 'admin'), true)), 
                    $Development > 1);
        }
        else {
            return new Platforms\Mysql\Platform(
                            new Platforms\PDO\Connection(
                                    new \PDO('mysql:host=localhost;dbname=StormTest', 'root', 'admin'), true), 
                    false);
        }
    }
    
    public function GetStorm() {     
        //return new Storm(new Mapping\BloggingDomainDatabaseMap());
        return new Api\Caching\Storm(new \Storm\Utilities\Cache\MemcacheCache('localhost'),
                self::GetPlatform(),
                function () {
                    return new Storm(new Mapping\BloggingDomainDatabaseMap());
                });
    }

    const Id = 39;
    
    const Persist = 0;
    const Retreive = 1;
    const Discard = 2;
    const Operation = 3;

    public function Run(Storm $BloggingStorm) {
        $BlogRepository = $BloggingStorm->GetRepository(Entities\Blog::GetType());
        $TagRepository = $BloggingStorm->GetRepository(Entities\Tag::GetType());
        
        $Action = self::Operation;
        $Amount = 1;

        $Last;
        for ($Count = 0; $Count < $Amount; $Count++) {
            $Last = $this->Act($Action, $BloggingStorm, $BlogRepository, $TagRepository);
        }

        return $Last;
    }

    private function Act($Action, Storm $BloggingStorm, Repository $BlogRepository, Repository $TagRepository) {
        $Id = self::Id;
        if ($Action === self::Persist) {
            $Blog = $this->CreateBlog();
            foreach ($Blog->Posts as $Post) {
                $TagRepository->PersistAll($Post->Tags->ToArray());
            }
            $TagRepository->SaveChanges();

            $BlogRepository->Persist($Blog);
            $BlogRepository->SaveChanges();
            
            return $Blog;
        } else if ($Action === self::Discard) {
            $BlogMap = $BloggingStorm->GetDomainDatabaseMap()->GetDomain()->GetEntityMap(Entities\Blog::GetType());

            $Request = new Pinq\Request($BlogMap);
            $Request->Where(function ($Blog) use(&$Id) {
                return $Blog->Id === $Id;
            });

            $BlogRepository->Discard($Request);
        } else if ($Action === self::Retreive) {
            $Outside = new \DateTime();
            $Outside->sub(new \DateInterval('P1D'));
                
               
            $RevivedBlog = $BlogRepository->Load($BlogRepository->Request()
                        ->Where(function ($Blog) use($Id, $Outside) {
                            $Foo = $Id;
                            $Sandy = 40;
                            $Sandy += $Id;

                            $ADate = new \DateTime();

                            $Awaited = $ADate->add(new \DateInterval('P2Y1DT15M')) > new \DateTime() || 
                                    acos(atan(tan(sin(pi()))));

                            $True = null === null && null !== false || false !== true && in_array(1, [1,2,3,4,5,6]);

                            $Possibly = $Foo . 'Hello' <> ';' || $Sandy == time() && $Outside->getTimestamp() > (time() - 3601);

                            $Maybe = $Blog->Description != 45 || (~3 - 231 * 77) . $Blog->Name == 'Sandwich' && $True || $Awaited;

                            return $Foo === $Blog->Id && (true || mt_rand(1, 10) > 10 || $Blog->Id === $Foo  || $Blog->CreatedDate < new \DateTime() && $Maybe || $Possibly);
                        })
                        ->OrderBy(function ($Blog) { return $Blog->Id . $Blog->CreatedDate; })
                        ->OrderByDescending(function ($Blog) { return $Blog->Id; })
                        ->GroupBy(function ($Blog) { return $Blog->Id; })
                        ->First());
                        
            //$RevivedBlog = $BlogRepository->LoadById($Id);
            if(extension_loaded('xdebug')) {
                var_dump($RevivedBlog);
            }
            $RevivedBlog->Posts[0]->Tags->ToArray();
            $RevivedBlog->Posts[1]->Tags->ToArray();
            
            return null;
            
        } else if ($Action === self::Operation) {
            $Procedure = $BlogRepository->Procedure(function ($Blog) {
                        $Blog->Description = md5(new \DateTime());
                        $Blog->Name .= strpos($Blog->Description, 'Test') !== false ?
                                'Foobar' . (string)$Blog->CreatedDate : $Blog->Name . 'Hi';
                        $Blog->CreatedDate = (new \DateTime())->diff($Blog->CreatedDate, true);
                    })
                    ->Where(function ($Blog) use ($Id) {
                        return $Blog->Id === $Id && null == null && (~3 ^ 2) < (40 % 5) && in_array(1, [1,2,3,4,5,6]);
                    }); 
            
            $BlogRepository->Execute($Procedure);
            
            $BlogRepository->SaveChanges();
            
        }
    }

    private function CreateBlog() {
        $Blog = new Entities\Blog();
        $Blog->Name = 'Test blog';
        $Blog->Description = 'The tested blog';
        $Blog->CreatedDate = new \DateTime();
        $Blog->Posts = new \Storm\Drivers\Base\Object\Properties\Collections\Collection(Entities\Post::GetType());
        $this->CreatePosts($Blog);

        return $Blog;
    }

    private function CreatePosts(Entities\Blog $Blog) {
        $Post1 = new Entities\Post();
        $Post1->Blog = $Blog;
        $Post1->Title = 'Hello World';
        $Post1->Content = 'What\'s up?';
        $Post1->CreatedDate = new \DateTime();
        $Post1->Tags = new \Storm\Drivers\Base\Object\Properties\Collections\Collection(Entities\Tag::GetType());
        $this->AddTags($Post1);
        $Blog->Posts[] = $Post1;

        $Post2 = new Entities\Post();
        $Post2->Blog = $Blog;
        $Post2->Title = 'Hello Neptune';
        $Post2->Content = 'What\'s going on nup?';
        $Post2->CreatedDate = new \DateTime();
        $Post2->Tags = new \Storm\Drivers\Base\Object\Properties\Collections\Collection(Entities\Tag::GetType());
        $this->AddTags($Post2);
        $Blog->Posts[] = $Post2;
    }

    public function AddTags(Entities\Post $Post) {
        $Names = ['Tagged', 'Tummy', 'Tailgater', 'Food Fight', 'Andy'];
        
        for ($Count = 100; $Count > 0; $Count--) {
            $Tag = new Entities\Tag();
            $Tag->Name = $Names[rand(0, count($Names) - 1)];
            $Post->Tags[] = $Tag;
        }
    }

}

return new Test();
?>