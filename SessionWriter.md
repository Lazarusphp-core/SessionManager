# SessionWriter guide and template

## The purpose

The purpose of the Session Writer is used to make a connection between the database and the session class, the Writer has been designed to take away the restrictions of using lazarusphp/Database manager and its predefined Database Queries and any dependencies this used.

## What can it do?

Now that the Sesion class is detached from the framework Creating your own SessionWriter allows for more freedom to implement your own database structure and Database queries along with other requirements that may be needed such as an expiry date, unique id or a date format.

## Do i have to use a SessionWriter?

Unfortunatly yes. this file is implemented in place and is a required by both the SessionClass and the SessionControl interface, failing to implement this will cause the Session class to not function as planned.

## Injection methods.

The Session Writer supports two injection methods, the first injects directly into the Sessions->init() method the second injects data into the Writer class.

### Injectimng into the init method

Injecting into the Init() method also know as custom boot is a method designed to add custom code created in the Writer class and inject it into the init function this can be accomplished like so.

```php
use App\System\Classes\Writers\SessionWriter.php;
// setting the constructor parameter to true will enable customBoot()
// Remove the true flag to disable.
$session = new Sessions(true);
$session->init([SessionWriter::class]);

// this will trigger the following code inside init.


 if (is_bool($this->customboot) && $this->customboot === true) {
    $this->sessionControl->customBoot();
}

```
A session Writer is required in order to function correctly as stated above however enabling customboot is not and can be left empty this will default to false.

calling init and custom boot must be done together but is only required once depending on the coding structure implemented and will not effect calling other new instantiations made.

## Injecting Data From the Session Writer
if Injecting directly into the session writer is needed this can be done like so.

```php

$session = new Sessions(true);
$session->init([SessionWriter::class],[
    "table"=>"sessions",
    "expity"=>7,
]);
```

Adding an array as a second parameter would pass the data to the session Writer class  making it possible to implement array values into the session writer itself like so.

```php


    private $config;
// Constructor

    public function __construct(array $config=null)
    {
        $this->config = $config;
    }

// Read Query
    public function readQuery($sessionID):mixed
    {
        $query = new QueryBuilder();
        $stmt = $query->sql("SELECT * FROM ".$this->config["table"]." WHERE session_id = :sessionID AND WHERE expiry=:expiry",[":sessionID"=>$sessionID,":expiry"=>$this->config["expiry"]])
        ->one(PDO::FETCH_ASSOC);
        return $stmt;
    }

```

by adding the support for Array injection would make it possible to change values on the fly.

#  The Template

## The Connections Requests.
From the point of reading it is assumed that a database connection is established and active.

the Session class and Writer are both connected using an interface called SessionComtrol this is required and located at Lazarusphp\SessionManager\Interfaces\SessionControl, and must be implemented on the SessionWriter class.

belows examples are taken from the codebase of lazarusphp/DatabaseManager the following method names are required


### customBoot()
```php
   public function customBoot():void
    {
        $this->setCookie();
    }
```

### openQuery
 this section of code is used to open the session connection,
```php
 public function openQuery():bool
    {
        return true;
    }
```


### closeQuery
close te Session Connection
```php
  public function closeQuery():bool
    {
        return true;
    }
```

### ReadQuery

select any data from the database table using sessionID, SessionId is passed from the  Sessions class all other properties are passed from within the Session Writer or injected on te class instantiated.



```php
 public function readQuery($sessionID):mixed
    {
        $query = new QueryBuilder();
        $stmt = $query->sql("SELECT * FROM ".$this->config["table"]." WHERE session_id = :sessionID",[":sessionID"=>$sessionID])
        ->one(PDO::FETCH_ASSOC);
        return $stmt;
    }
```

### WriteQuery
Write Session Data to the database
```php
    public function writeQuery($sessionID,$data):bool
    {
        $date = Date::withAddedTime("now","P".$this->config["expiry"]."D")->format($this->config["format"]);
        $params =  [":sessionID"=>$sessionID,":data"=>$data,":expiry"=>$date];
        $query = new QueryBuilder();
        $query->asQuery("REPLACE INTO " . $this->config["table"] . " (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)",$params);
        return true;
    } 
```

### DestroyQuery
in order to destroy a session  simply use the command session_destroy() or unset]
```php
    public function destroyQuery($sessionID): bool
    {
        $query = new QueryBuilder();
        $params = [":sessionID"=>$sessionID];
        $query->asQuery("DELETE FROM " . $this->config["table"] . " WHERE session_id=:sessionID",$params);
        return true;
    }
```

### Garbage Collection
garbage collection is designed to clear out exired sessions, this can be accomplished by using session_gc();
```php
    public function gcQuery():void
    {
        $expiry = Date::create("now");
        $expiry = $expiry->format("y-m-d h:i:s");

        try {
            $query = new QueryBuilder();
            $params = [":expiry"=>$expiry];
            $query->asQuery("DELETE FROM sessions WHERE expiry  < :expiry",$params);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . $e->getCode());
        }
    }
```

### other information 
some methods do not need to be called directly and still use the default php functions like session_destroy and session_gc there is no need to use $session->gc as this would fail.

when changing the query commands and coding structure always remeber return the correct value.