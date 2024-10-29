# Lazarusphp Session Manager
SessionManager from lazarusphp is a Work in progress Database Driven Session handler designed to giving easier access and control over Sessions.

## Installing Sessions Manager

```
composer require lazarusphp/sessionmanager
```

## The Basics

### Starting Session Manager.
instantiating sessions is done by running the start sessions, The start session runs the session handler and varifies the status of a session. 

once varified a session is started, be aware that all sessions are stored within a databse upon instantiation.

```php
use lazarusphp\SessionsManager\Sessions;
$session = new Sessions();
$session->start();
```

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

### Designating a different Table name.
By Default the Database Table name is sessions, however this can be changed using the setTable() method like so

```php
use LazaruysPhp\SessionsManager\Sessions;
$session = new Sessions();
$session->setTable("newTableName");
```
> Any Session names will automaitically be converted to lowercase upon submissions.


### Notes

> this class cannot currently be used as a standalone script