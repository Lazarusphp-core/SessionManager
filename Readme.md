# Lazarusphp Session Manager

#### Index 


* [Whats new](#whats-new)
* [What is the Session manager](#what-is-the-session-manager)
* [Installing the Session Manageer](#installing-sessions-manager)


* [Creating and Viewing Sessions](#creating-and-viewing-sessions)
* [Deleting Sessions](#deleting-sessions)
* [Garabage Collection](#garbage-collection)


## Whats new?

* New Instantiation Method 
* Code Clean up
* injection of SessionWriter classes
* Supports upto php version 8.4
* optional Config values

# Whats been removed
* Custom boot flags

## What is the session manager.
The Session manager is a Database driven Session handler, storing all session data within a database allowing for more control.

## Installing Sessions Manager

```
composer require lazarusphp/sessionmanager
```


## The Basics
the Session manager can be called at anytime within a website, however a first time instantiation to connect to a database is required.

### Instantiation

This script is a standalone script that demonstrates how to instantiate a session manager using the SessionsManager class from the LazarusPhp namespace. It requires the SessionWriter class to work correctly. This setup allows users to customize their own database preferences for session management.

```php
use LazarusPhp\SessionsManager\Sessions;
$session = new Sessions();
$sessions->instantiate([SessionWriter::class]);
```

optionally a second parameter $config can be added into the instantiate method, this is a set of key pair arrays  and can be done like so

```php
use LazarusPhp\SessionsManager\Sessions;
$session = new Sessions();
$sessions->instantiate([SessionWriter::class],
[
    "table"=>"Sessions",
    "days"=>7
]);
```

SessionManager class also offers a Factory Class option giving a quicker more effient method to call and instantiate the class.

```php
use LazarusPhp\SessionsManager\SessionsFactory as Sessions;

Sessions::instantiate([SessionWriter::class],
[
    "table"=>"Sessions",
    "days"=>7
]);
```

### Creating and Viewing Sessions
sessions can be created on the fly, this is done using the following code.

this can be done using the Factory class method as well.
```php
use lazarusphp\SessionsManager\Sessions;
$session = new Sessions();
$session->username = "jack";
$session->dob = "03/12/1975";
```

```php
use lazarusphp\SessionsManager\SessionsFactory as Sessions;
Sessions::set("username","jack");
Sessions::set("dob","03/12/1975");
```

in order to view the newly created sessions simply call the property without assigning a value.

like settings  session this can also be accomplished using the SessionsFactory class

```php
use lazarusphp\SessionsManager\Sessions;
$session = new Sessions();
echo "Hello " . $session->username;
```

```php
use lazarusphp\SessionsManager\SessionsFactory as Sessions;
echo "Hello" . Sessions::get("username");
```

## Extra Session Options

### Deleting Sessions

> this Documentation is still available but the function has been removed temporarily

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

