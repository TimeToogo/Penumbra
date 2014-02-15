Storm - Still in development
============================

Domain oriented ORM, crafted with passion for PHP 5.4+.


The goals of Storm
==================
 - To provide a maintainable and sensible approach to the complex ORM realm.
 - To reward the user with unpolluted and flexible domain models.
 - To provide a fluent [language integrated query (Similar to C#'s LINQ)](#phpinq)
 - To [reduce the amount of queries](#queries) to the underlying platform and assist in [eliminating n+1 queries](#queries).

What Storm is not
=================
 - The much loved active record pattern (although possible in the future).
 - The answer to all your problems
 - Stable or complete - Storm needs contributers!

 
<a name="phpinq"></a>Storm - PHP integerated query
==================================================
 - Code in terms of the domain, not your database (SQL).
 - Abstract yourself from the underlying database.
 - Remove the hassle of magic strings and prevent SQL injection.

**Requests (`SELECT`) - Example:**
```php
$MiddleAgedUsersRequest = $UserRepository->Request()
        ->Where(function (User $User) {
            return $User->GetAge() > 20 && $User->GetAge() < 50 && $User->IsActive();
        })
        ->OrderByDescending(function (User $User) { return $User->GetLastLoginDate(); })
        ->Limit(20)
        ->AsArray();
        
$SomeActiveMiddleAgedUsers = $UserRepository->Load($MiddleAgedUsersRequest);
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

$UserRepository->Execute($InactiveUserProcedure);
```

*NOTE: The supplied functions never actually executed, they are parsed and then mapped to the underlying platform as queries.*


<a name="queries"></a>Storm - Sensible querying
===============================================
**One main goal of Storm is to take advantage of the underlying database as much as possible.**
 - Storm will use batch inserts/upserts and deletes where possible.
 - Elimination of the dreaded N+1 query scenario introduced by excessive lazy loading. With storm, there are currenlty multiple relationship loading implementations:
     - `Eager` - Relationships are loaded along with the parent and children are joined where appropriate.
     - `SemiLazy` - Relationships are not loaded with the parent entity but when one is required, globally every unloaded relationship will be loaded .
     - `Lazy` (Recommended) - Relationships are loaded when required for all entities in the request object graph.
     - `ExtraLazy` (N+1 likely) - Relationships are loaded when required from for each parent entity.



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
 - `SemiLazy` - **4** For loading a single parent in a request the will be equivalent as `Lazy` mode below.
 - `Lazy` - **4** (the user | posts | all authors | all tags), the posts will be loaded when `$User->GetPosts()` is iterated and all the tags will be loaded when the first `$Post->GetTags()` is iterated.
 - `ExtraLazy` - **(N * 2) + 2** - (user | posts | *each post's* author | *each post's* tags).

*NOTE: Relationship loading mode is per-relationship, the above example assumes every relationship will have one loading mode for simplicity.
