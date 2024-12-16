# Lazarusphp Session Manager

#### Index#

    
* [Whats new ]
* [What is the Session manager](#what-is-the-session-manager)
* [Installing the Session Manageer](#installing-sessions-manager)
* [Starting the Session manager](#starting-session-manager)
* [The CustomBoot Flag](#the-customboot-flag)
* [The Session Writer](#the-session-writer)
* [Injecting Parameters  into Session Writer](#injecting-parameters-into-sessionwriter)    
* [Creating and Viewing Sessions](#creating-and-viewing-sessions)
* [Deleting Sessions](#deleting-sessions)
* [Garabage Collection](#garbage-collection)


## Whats new?

* New Instantiation Method 
* Code Clean up
* injegration of SessionWriter classes

## What is the session manager.
The Session manager is a Database driven

## Installing Sessions Manager

```
composer require lazarusphp/sessionmanager
```

## The Basics

### Starting Session Manager.
Instantiating sessions is done by creating a new first time instantiation of the class and the init() method the first time instantiation utilises 3 parameters one is required and two are  optional.

#### the customBoot flag 
 enabling the customboot flag will give the ability to inject external code into the init() method this code is injected using a SessionWriter class 

leaving the new instantiation blank will default the customboot flag to false and will just start the session as normal.

```php
// Enabling  customboot
$session = new sessions(true)
```

#### The Session Writer

the Session Writer class allows the ability to pass a custom set of database commands to the session class, this is a required component of the init method and eliminates te need to modify the Session class itself when installed.

the sessionWriter:: class i injected into the init() method like so

```php
// customboot does not have to be enabled with to work with init()
$session = new Sessions(true);
$session->init([SessionWriter::class]);
```

#### Injecting parameters into SessionWriter

similar to the option of injecting code into the Sessions Class the init method provide the ability to pass array data into the Session Writers constructor. this allow custom data to be manipulated on the fly from outside of the Session Writer.

```php
// customboot does not have to be enabled with to work with init()
$session = new Sessions(true);
$session->init([SessionWriter::class],[
    "table"=>"sessions",
    "expiry"=>7,
    "date_format"=>"y-m-d h:i:s",
]);
```
upon entering this data into the Session Writer it can be used and manipulated as needed.

[Click here]() for how to use Session Writers.

depending on personal coding preferences using customboot and init  should only need to be called once. it is required that a database connection should be established before calling any session classes.

### Creating and Viewing Sessions
sessions can be created on the fly, this is done using the following code.

```php
use lazarusphp\SessionsManager\Sessions;
$session = new Sessions();
$session->username = "jack";
$session->dob = "03/12/1975";
```

in order to view the newly created sessions simply call the property without assigning a value.

```php
use lazarusphp\SessionsManager\Sessions;
$session = new Sessions();
echo "Hello " . $session->username;
```

## Extra Session Options

### Deleting Sessions
the Session manager has the ability to remove individual Sessions or can remove them all as a whole by using the deleteSessions() method;

```php
use Lazarusphp\SessionsManager\Sessions;

$sessions = new Sessions();
// Delete Specific Sessions
$session->deleteSessions("username","email","password");

// Delete All Sessions
$sessions->deleteSessions();
```
Be Aware that deleting all Sessions (Not Specifying Sessions) Will do a session_destroy() call and will also delete the entire session from the database whereas choosing the sessions will only remove the specific values from the database, keeping the session intact. this will override anything currently set by the server.


### Garbage Collection 
Garbage collection is a built in function from php and is used to delete Generated Sessions which have expired, this can be done by using the session_gc function

```php
session_gc();
```

once called the script will go through all the sessions and will delete any stale records which  hold an expiry set before the current timestamp.

