<?php 

namespace StormExamples\One;

use \StormExamples\One\Entities;
use \Storm\Api;
use \Storm\Api\Base\Storm;
use \Storm\Api\Base\Repository;
use \Storm\Drivers\Platforms;
use \Storm\Drivers\Platforms\Development\Logging;
use \Storm\Drivers\Base\Object\Properties\Proxies;

class One implements \StormExamples\IStormExample {
    const DevelopmentMode = 1;
    const UseCache = false;
    
    public static function GetPlatform() {
        return new Platforms\Mysql\Platform(self::DevelopmentMode > 1);
    }
    
    private static function GetConnection() {
        $PDOConnection = Platforms\PDO\Connection::Connect('mysql:host=localhost;dbname=StormTest', 'root', 'admin');
              
        return new Logging\Connection(self::DevelopmentMode > 0 ? new Logging\DumpLogger() : new Logging\NullLogger(), 
                $PDOConnection);
    }
    
    
    private static function GetProxyGenerator() {
        return new Proxies\DevelopmentProxyGenerator(
                __NAMESPACE__ . '\\' . 'Proxies', 
                __DIR__ . DIRECTORY_SEPARATOR . 'Proxies');
    }
    
    public function GetStorm() {
        $Cache = self::UseCache && class_exists('Memcache', false) ? new \Storm\Utilities\Cache\MemcacheCache('localhost') : null;
        
        $Configuration = new Api\DefaultConfiguration(
                Mapping\BloggingDomainDatabaseMap::Factory(), 
                self::GetConnection(),
                self::GetProxyGenerator(),
                $Cache);
        
        return $Configuration->Storm();
    }
    
    const Id = 500;
    
    const Persist = 0;
    const Retreive = 1;
    const RetreiveComplex = 2;
    const PersistExisting = 3;
    const Discard = 4;
    const Procedure = 5;
    
    public function Run(Storm $BloggingStorm) {
        $BlogRepository = $BloggingStorm->GetRepository(Entities\Blog::GetType());
        $TagRepository = $BloggingStorm->GetRepository(Entities\Tag::GetType());
        $AuthorRepository = $BloggingStorm->GetRepository(Entities\Author::GetType());
        
        $Action = self::RetreiveComplex;
        
        $Amount = 1;        
        $Last;
        for ($Count = 0; $Count < $Amount; $Count++) {
            $Last = $this->Act($Action, $BloggingStorm, $BlogRepository, $AuthorRepository, $TagRepository);
        }

        return $Last;
    }

    private function Act($Action, Storm $BloggingStorm, Repository $BlogRepository, Repository $AuthorRepository, Repository $TagRepository) {
        $Id = self::Id;
        switch ($Action) {
            case self::Persist:
                return $this->Persist($Id, $BloggingStorm, $BlogRepository, $AuthorRepository, $TagRepository);


            case self::Retreive:
                return $this->Retreive($Id, $BloggingStorm, $BlogRepository, $TagRepository);


            case self::RetreiveComplex:
                return $this->RetreiveComplex($Id, $BloggingStorm, $BlogRepository, $TagRepository);

                
            case self::PersistExisting:
                return $this->PersistExisting($Id, $BloggingStorm, $BlogRepository, $TagRepository);

                
            case self::Procedure:
                return $this->Procedure($Id, $BloggingStorm, $BlogRepository, $TagRepository);


            case self::Discard:
                return $this->Discard($Id, $BloggingStorm, $BlogRepository, $TagRepository);

            default:
                return null;
        }
    }
    
    private function Persist($Id, Storm $BloggingStorm, 
            Repository $BlogRepository, 
            Repository $AuthorRepository,
            Repository $TagRepository) {
        
        $Blog = $this->CreateBlog();
        foreach ($Blog->Posts as $Post) {
            $TagRepository->PersistAll($Post->Tags->getArrayCopy());
            $AuthorRepository->Persist($Post->Author);
        }
        $BloggingStorm->SaveChanges();

        $BlogRepository->Persist($Blog);
        $BlogRepository->SaveChanges();

        return $Blog;
    }
    
    private function Retreive($Id, Storm $BloggingStorm, Repository $BlogRepository, Repository $TagRepository) {
        $RevivedBlog = $BlogRepository->LoadById($Id);
        if($RevivedBlog === null) {
            throw new \Exception("Entity with id: $Id does not exist");
        }
        if(extension_loaded('xdebug')) {
            var_dump($RevivedBlog);
        }
        $Post = $RevivedBlog->Posts[0];
        $Author = $Post->Author;
        $Test = $Author->FirstName;
        $Foo = $RevivedBlog->Posts[1]->Tags->getArrayCopy();
        $BlogRepository->GetIdentityMap()->Clear();
        
        return null;
    }
    
    private function RetreiveComplex($Id, Storm $BloggingStorm, Repository $BlogRepository, Repository $TagRepository) {
        $Outside = new \DateTime();
        $Outside->sub(new \DateInterval('P1D'));

        $Array = [1,2,3,4,5,6];
        $RevivedBlog = 
                $BlogRepository->Request()
                ->Where(function ($Blog) use($Id, $Outside, $Array) {
                    $Foo = $Id;
                    $Sandy = 40;
                    $Sandy += $Id;

                    $ADate = new \DateTime();

                    $Awaited = $ADate->add(new \DateInterval('P2Y1DT15M')) > new \DateTime() || 
                            acos(atan(tan(sin(pi()))));

                    $True = null === null && null !== false || false !== true && in_array(1, $Array);

                    $Possibly = $Foo . 'Hello' <> ';' || $Sandy == time() && $Outside->getTimestamp() > (time() - 3601);

                    $Maybe = $Blog->Description != 45 || (~3 - 231 * 77) . $Blog->GetName() == 'Sandwich' && $True || $Awaited;
                    
                    return (~1 - 500 ^ 2) && $Foo === $Blog->Id && (true || mt_rand(1, 10) > 10 || $Blog->Id === $Foo  || $Blog->CreatedDate < new \DateTime() && $Maybe || $Possibly);
                })
                ->OrderBy(function ($Blog) { return $Blog->Id . $Blog->CreatedDate; })
                ->OrderByDescending(function ($Blog) { return $Blog->Id; })
                ->GroupBy(function ($Blog) { return $Blog->Id; })
                ->First();
        
        if($RevivedBlog === null) {
            throw new \Exception("Entity with id: $Id does not exist");
        }
        if(extension_loaded('xdebug')) {
            var_dump($RevivedBlog);
        }
        $RevivedBlog->Posts[0]->Tags->getArrayCopy();
        $RevivedBlog->Posts[1]->Tags->getArrayCopy();
        $BlogRepository->GetIdentityMap()->Clear();

        return null;
    }
    
    private function AggregationOptions(Repository $BlogRepository) {
        //#1 ----------------------------------------------------- Yes
        $Request = $BlogRepository->Request();
        
        $Request->Where(function ($Blog) use($Id) {
                    return $Blog->Id === $Id;
                })
                ->OrderBy(function ($Blog) { return $Blog->Id . $Blog->CreatedDate; })
                ->OrderByDescending(function ($Blog) { return $Blog->Id; })
                ->GroupBy(function ($Blog) { return $Blog->Name; })
                ->Select(function ($Blog, \Storm\Pinq\IAggregate $Blogs) {
                    return [
                        'Full Name' => $x,
                        'Name' => $Blog->GetName(),
                        'MaxHits' => $Hits->Maximum(),
                        'MinHits' => $Hits->Minimum(),
                        'Sum' => $Hits->Sum(),
                        'Implode' => $Hits->Implode(', '),
                        'Count' => $Hits->Count(),
                        'AllBig' => $Blogs->All(function ($Blog) { return $Blog->Hits > 50; }),
                    ];
                });
                
        //#1 ----------------------------------------------------- Yes
        $Sums = $BlogRepository->Request()
                ->Where(function ($Blog) use($Id) {
                    return $Blog->Id === $Id;
                })
                ->OrderBy(function ($Blog) { return $Blog->Id . $Blog->CreatedDate; })
                ->OrderByDescending(function ($Blog) { return $Blog->Id; })
                ->GroupBy(function ($Blog) { return $Blog->Name; })
                ->Sum(function ($Blog) { return $Blog->GetHits(); });
                
                
                
                
        //#2 -----------------------------------------------------NO
        $BlogRepository->Request()
                ->Where(function ($Blog) use($Id) {
                    return $Blog->Id === $Id;
                })
                ->OrderBy(function ($Blog) { return $Blog->Id . $Blog->CreatedDate; })
                ->OrderByDescending(function ($Blog) { return $Blog->Id; })
                ->GroupBy(function ($Blog) { return $Blog->Name; })
                ->Data([
                    'MaxHits' => function ($Blog) { return $Blog->GetHits(); },
                    'MaxHits' => function ($Blog) { return $Blog->GetHits(); },
                    'MinHits' => function ($Blog) { return $Blog->GetHits(); },
                    'Sum' => function ($Blog) { return $Blog->GetId(); },
                    'Implode' => function ($Blog) { return $Blog->GetHits(); },
                    'Count' => function ($Blog) { return $Blog; },
                ]);
        
        //#3 ----------------------------------------------------- Possibly
        $BlogRepository->RequestData()
                
                ->Value(function ($Blog) { return $Blog->Name; })->As('Name')
                ->Maximum(function ($Blog) { return $Blog->GetHits(); })->As('MaxHits')
                ->Minimum(function ($Blog) { return $Blog->GetHits(); })->As('MinHits')
                ->Sum(function ($Blog) { return $Blog->GetHits(); })->As('Sum')
                ->Implode(', ', function ($Blog) { return $Blog->GetHits(); })->As('Implode')
                ->Count()->As('Count')
                
                ->Where(function ($Blog) use($Id) {
                    return $Blog->Id === $Id;
                })
                ->OrderBy(function ($Blog) { return $Blog->Id . $Blog->CreatedDate; })
                ->OrderByDescending(function ($Blog) { return $Blog->Id; })
                ->GroupBy(function ($Blog) { return $Blog->Name; })
                
                ->Load()
                ;
              
        //THE GOAL:---
        $Request->Where(function ($Blog) use ($Request) { return $Request->All(function ($Blog) { return $Blog->Id % 10 === 0; }); })
                ->GroupBy(function ($Blog) { return $Blog->GetYear(); })
                ->Select(function (\Storm\Pinq\IAggregate $Blogs) {
                    $Hits = function ($Blog) { return $Blog->Hits; };
                    $Blog = $Blogs->First();
                    return [
                        'Full Name' => $Blog->GetId(),
                        'Name' => $Blog->GetName(),
                        'MaxHits' => $Blogs->Maximum($Hits),
                        'MinHits' => $Blogs->Minimum($Hits),
                        'Sum' => $Blogs->Sum($Hits),
                        'Implode' => $Blogs->Implode(', ', $Hits),
                        'Count' => $Blogs->Count(),
                        'AllBig' => $Blogs->All(function ($Blog) { return $Blog->Hits > 50; }),
                    ];
                });
        /**
         * SELECT 
         *      Id AS `Full Name`,
         *      Name AS `Name`,
         *      MAX(Hits) AS `MaxHits`,
         *      Min(Hits) AS `MinHits`,
         *      SUM(Hits) AS `Sum`,
         *      GROUP_CONCAT(Hits SEPARATOR ', ') AS `Implode`,
         *      COUNT(*) AS `Count`,
         *      BIT_AND(IF(Hits > 50, 1, 0)) AS `AllBig`
         * FROM Blogs WHERE (SELECT BIT_AND(IF(Id, 1, 0)) FROM Blogs WHERE Id % 10 = 0) 
         * GROUP BY Year
         */
    }
    
    private function PersistExisting($Id, Storm $BloggingStorm, Repository $BlogRepository, Repository $TagRepository) {
        
        $Blog = $BlogRepository->LoadById($Id);
        $Blog->Posts[0]->Content = 'foobar';
        $Blog->Posts[0]->Author->FirstName .= 'a';
        $Blog->Posts[1]->Content = 'BarBar---------------!';
        
        $BlogRepository->Persist($Blog);
        $BlogRepository->SaveChanges();

        return $Blog;
    }
    
    private function Procedure($Id, Storm $BloggingStorm, Repository $BlogRepository, Repository $TagRepository) {
        $BlogRepository->Procedure()
                ->Where(function ($Blog) use ($Id) {
                    return $Blog->Id === $Id && null == null && (~3 ^ 2) < (40 % 5) && in_array(1, [1,2,3,4,5,6]);
                })
                ->Execute([$this, 'UpdateBlog']);

        $BlogRepository->SaveChanges();
    }
    
    public function UpdateBlog(Entities\Blog $Blog) {
        $Blog->Description = hash('sha1', $Blog->GetName());

        $Blog->SetName(substr($Blog->GetName() . (strpos($Blog->Description, 'Test') !== false ?
                'Foobar' . (string)$Blog->CreatedDate : $Blog->GetName() . 'Hi'), 0, 50));

        $Blog->CreatedDate = (new \DateTime())->add((new \DateTime())->diff($Blog->CreatedDate, true));
    }
    
    private function Discard($Id, Storm $BloggingStorm, Repository $BlogRepository, Repository $TagRepository) {

        $BlogRepository->Discard($BlogRepository->LoadById($Id));
        $BlogRepository->Remove()
                ->Where(function (Entities\Blog $Blog) use ($Id) { return $Blog->Id === $Id; })
                ->Skip(0)
                ->Limit(1)
                ->OrderByDescending(function (Entities\Blog $Blog) { return mt_rand(); })
                ->Execute();
                
        $BlogRepository->SaveChanges();
    }
    
    
    private function CreateBlog() {
        $Blog = new Entities\Blog();
        $Blog->Name = 'Test blog';
        $Blog->Description = 'The tested blog';
        $Blog->CreatedDate = new \DateTime();
        $Blog->Posts = new \ArrayObject([]);
        $this->CreatePosts($Blog);

        return $Blog;
    }

    private function CreateAuthor() {
        $FirstNames = ['Joe', 'Jack', 'Bill', 'Tom', 'Sandy', 'Mat'];
        $LastNames = ['Runt', 'Paffy', 'Derka', 'Shammy', 'Tuple', 'White'];
        
        $Author = new Entities\Author();
        $Author->FirstName = $FirstNames[rand(0, count($FirstNames) - 1)];
        $Author->LastName = $LastNames[rand(0, count($LastNames) - 1)];
        
        return $Author;
    }

    private function CreatePosts(Entities\Blog $Blog) {
        $Post1 = new Entities\Post();
        $Post1->Blog = $Blog;
        $Post1->Author = $this->CreateAuthor();
        $Post1->Title = 'Hello World';
        $Post1->Content = 'What\'s up?';
        $Post1->CreatedDate = new \DateTime();
        $Post1->Tags = new \ArrayObject([]);
        $this->AddTags($Post1);
        $Blog->Posts[] = $Post1;

        $Post2 = new Entities\Post();
        $Post2->Blog = $Blog;
        $Post2->Author = $this->CreateAuthor();
        $Post2->Title = 'Hello Neptune';
        $Post2->Content = 'What\'s going on nup?';
        $Post2->CreatedDate = new \DateTime();
        $Post2->Tags = new \ArrayObject([]);
        $this->AddTags($Post2);
        $Blog->Posts[] = $Post2;
    }

    public function AddTags(Entities\Post $Post) {
        $Names = ['Tagged', 'Tummy', 'Tailgater', 'Food Fight', 'Andy'];
        
        for ($Count = 500; $Count > 0; $Count--) {
            $Tag = new Entities\Tag();
            $Tag->Name = $Names[rand(0, count($Names) - 1)];
            $Tag->Description = 'This is a description - ' . $Count;
            $Tag->Number = $Count * $Count;
            $Post->Tags[] = $Tag;
        }
    }

}

return new One();
?>