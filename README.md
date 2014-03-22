Storm - Still in development
============================

Domain oriented ORM, crafted with passion for PHP 5.4+.

Storm is not finished, the foundation and architecture is there, but the 
codebase has almost no unit tests. Since storm was originally a learning exercise 
with the goal of creating an orm that does not influence the domain model, this 
project has maintained a good code quality, but it is quickly becoming apparent 
that I cannot single handedly develop and maintain this project, especially with 
school. Serious contributors are needed, please feel free to clone the repository 
and look around the code base, if you have any questions, inquiries or want to begin 
contributing please email me at: [elliot@aanet.com.au](mailto:elliot@aanet.com.au).

Please be aware that this is my first large open source project, so any tips/recommendations are welcome.

Another ORM for PHP?!
=====================
Yes! I built this to learn, but now I see that it offers features that no
other ORM has. Storm is 100% data mapper and aims to stay [completely decoupled](#domainmodels)
from your domain models while offering a [powerful OO query api](#pinq). 

Summary of the current state of Storm
=====================================
 - ORM [functionality is running](https://github.com/TimeToogo/Storm/blob/master/Example/One/Example.php) but far from stable.
 - Overall, maintains a [good code quality](https://scrutinizer-ci.com/g/TimeToogo/Storm).
 - Plenty of work is required just [to set up](https://github.com/TimeToogo/Storm/tree/master/Example/One).
 - Decent size codebase (~15,000 LOC).
 - [API](https://github.com/TimeToogo/Storm/tree/master/Storm/Storm/Api) structure is not finialized.
 - Apalling [test suite](https://github.com/TimeToogo/Storm/tree/master/Tests/Storm/Tests).
 - Lacking in documentation / code comments.
 - Serious contributors [are needed](mailto:elliot@aanet.com.au).

The goals of Storm
==================
 - To provide a maintainable and sensible approach to the complex ORM realm.
 - To reward the user with unpolluted and [flexible domain models](#domainmodels).
 - To provide a fluent [language integrated query (Similar to C#'s LINQ)](#pinq)
 - To [reduce the amount of queries](#queries) to the underlying platform and assist in [eliminating n+1 queries](#queries).

<a name="domainmodels"></a>Flexible domain models
==================================================
**Storm aims to provide as little restriction to domain models as possible**
 - Storm natively supports: fields, getters/setters and even indexors or innvocation as entity properties.
 - Your entities can remain completely unaware of Storm: no base class, no annotations and no persistence logic.
 - Transparent relationship loading and persisting.
 - Seamless identifying and non-identifying relationships between entities:
     - Required child entity - A `User` has a `Profile`
     - Optional child entity - A `User` may have a `CreditCard`
     - [Array](http://php.net/manual/en/language.types.array.php)/[Traversable](http://au1.php.net/manual/en/class.traversable.php) of many child entities - A `User` has multiple `Posts`, *Traversable must be used for lazy loading*
 - Embedded Objects
 - Value Objects
 - Polymorphic Types

<a name="pinq"></a>PHP integerated query (Pinq)
==============================================
 - Query in terms of the domain, not your database.
 - Abstract yourself from the underlying database.
 - Maintain full IDE auto-completion.
 - Remove the hassle of magic strings and prevent SQL injection.
 - Powerful aggregation api, `Count, Maximum, Minimum, Average, Sum, Implode, All, Any`
 - Supports used variables for parameterized queries `function ($_) use ($Value) {...`
 - Supports dynamic fields, method calls, function calls etc.
 - Even supports relationship properties for seamless joins!

**Entity Request (`SELECT`) - Example:**
```php
$MiddleAgedUsersRequest = $UserRepository->Request()
        ->Where(function (User $User) {
            return $User->GetAge() > 20 && $User->GetAge() < 50 ;
        })
        ->OrderByDescending(function (User $User) { return $User->GetLastLoginDate(); });
        
$SomeActiveMiddleAgedUsers = $MiddleAgedUsersRequest->AsArray();
```
Will map to something along the lines of:
```sql
SELECT Users.* FROM Users 
WHERE Users.Age > 20 AND Users.Age < 50
ORDER BY Users.LastLoginDate DESC;
```
**Complex Data Request (`SELECT`) - Example:**
```php
$UserStatistics = $UserRepository->Request()
        ->From($MiddleAgedUsersRequest)
        ->Where(function (User $User) { return  $User->IsActive(); })
        ->GroupBy(function (User $User) { return $User->GetAge(); })
        ->Select(function (User $User, IAggregate $Users) {
            return [
                'Age' => $User->GetAge(),
                'Amount' => $Users->Count(),
                'AverageVisits' => $Users->Average(function (User $User) { return $User->GetVisitsPerDay(); })),
            ];
        });
```
Will map to something along the lines of:
```sql
SELECT Users.Age AS Age, COUNT(*) AS Amount, AVG(Users.VisitsPerDay) AS AverageVisits FROM 
    (SELECT Users.* FROM Users 
    WHERE Users.Age > 20 AND Users.Age < 50
    ORDER BY Users.LastLoginDate DESC) AS Users
WHERE Users.IsActive
GROUP BY Users.Age;
```


**Procedures (`UPDATE`) - Example:**
```php
$InactiveUserProcedure = $UserRepository->Procedure(
        function (User $User) {
            $User->SetIsActive(false);
            $User->SetInactivationDate(new DateTime());
        })
        ->Where(function (User $User) {
            return $User->GetLastLoginDate() < (new DateTime())->sub(new DateInterval('P2Y'));
        }); 

$InactiveUserProcedure->Execute();
```
Will map to something along the lines of:
```sql
UPDATE Users SET 
    Users.IsActive = 0,
    Users.InactivationDate = NOW()
WHERE Users.LastLoginDate < DATE_SUB(NOW(), INTERVAL 2 YEAR)
```

*NOTE: The supplied functions never actually executed, they are parsed and then mapped to the underlying platform as queries.*


<a name="queries"></a>Sensible queries
======================================
**One main goal of Storm is to take advantage of the underlying database as much as possible.**
 - Storm will use batch inserts/upserts and deletes where possible.
 - Elimination of the dreaded N+1 query scenario introduced by excessive lazy loading. With storm, there are currenlty multiple relationship loading implementations:
     - `Eager` - Relationships are loaded along with the parent and children are joined where appropriate.
     - `Global scope lazy` - Relationships are not loaded with the parent entity but when one is required, globally every unloaded relationship will be loaded.
     - `Request scope lazy` (Recommended) - Relationships are loaded when required for all entities in the request object graph.
     - `Parent scope lazy` (N+1 likely) - Relationships are loaded when required from for each parent entity.



**Example:**

User is the parent entity, a user has many posts and a post has an author and many tags.
*Here is the typical N+1 scenario.*
```php
$User = $UserRepository->LoadById(1);
foreach($User->GetPosts() as $Post) {
    $Post->GetAuthor()->GetFullName();
    foreach($Post->GetTags() as $Tag) {
        $Tag->GetName();
    }
}
```
Now here is the number of queries executed for each loading mode (N=Number of Posts);
 - `Eager` - **3** (the user | posts joined with the author | all tags), when the user is loaded.
 - `Global scope lazy` - For loading a single request this is equivalent to `Request scope lazy` mode below.
 - `Request scope lazy` - **4** (the user | posts | all authors | all tags), the posts will be loaded when they are iterated and all the tags will be loaded when it is first is iterated.
 - `Parent scope lazy` - **(N * 2) + 2** - (user | posts | *each post's* author | *each post's* tags).

*NOTE: Relationship loading mode is per-relationship, the above example assumes every relationship will have the same loading mode for simplicity.*
