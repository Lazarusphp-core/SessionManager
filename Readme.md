# Lazarusphp Session Manager
SessionManager from lazarusphp is a Work in progress Database Driven Session handler designed to give more control over user sessions.

## Requirments.
* Understanding of php.
* Lazarusphp Database Manager Class
* the Ability to install composer and understaning of how to use its commands.

## How to install?
Installing this package is done using composers require method and is done like so. 
```
composer require lazarusphp/sessionmanager

composer dumpautoload -o
```

## How to instantiate the Session manager ?
In order to start the Session class you are required to instantiate the Session Handler this is done like so.

```php
use LazarusPhp\SessionManager\Sessions;
(new Sessions())->instantiate();
```
this can also be done by attaching a property name to the method like so

```php
use LazarusPhp\SessionManager\Sessions;
$session = new Sessions();
$session->instantiate();
```
the default Mysql table name for sessions is called sessions if you wish to change the database table name please add the value within the parenthesis

lets use users_sessions as an example


```php
use LazarusPhp\SessionManager\Sessions;
(new Sessions())->instantiate("users_sessions");
```
## Starting Sessions
in order for sessions to be started and stored within the database a session must be started this can be done like so.
```php
use LazarusPhp\SessionManager\Sessions;
$session->start();
```
although session_start() would work, the start method checks and verifys the session status, once verified the session name and id will be stored within a cookie allowing an active connection when the browser is closed.

```php
use LazarusPhp\SessionManager\Sessions;
$session->start();
```
By default the Session expiry time is set to 1 days  this is stored in both the Database in a cookie itself, this cookie is mandatory to stop the browser from regenerating a fresh id everytime it reloads.

Adding the number of days as shown below will change the expiry on in the database and cookie.

```php
use LazarusPhp\SessionManager\Sessions;
$session->start(14);
```

### Assigning a session

In order to create and assign a session this can be accomplished by doing one of two methods, the fist by using $_SESSION[] otherwise you can simply create an on the fly method like so.

```php
use Lazarusphp\SessionsManager\Sessions
$session = new Sessions();
$session->username = "test";
$session->email = "Email@email.com";
$session->password = "Test124";
```

The fowllowing snippet will simply replicate $_SESSION['username'] = "test";

### Garbage Collection 
Garbage collection is a built in function from php and is used to delete Generated Sessions which have expired, this can be done by using the session_gc function

```php
session_gc();
```

once called the script will go through all the sessions and will delete any stale records which  hold an expiry set before the current timestamp.

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

### Timezone Management (development)
if you wish to change the timezone for the session time  this can be done using the setTz() method by default this is set to Europe/London

```php
use Lazarusphp\SessionManager\Sessions;
$session = new Sessions();
$session->setTz();
```

by using the getTz() you will be able retrieve the currently set timezone

```php
use Lazarusphp\SessionManager\Sessions;
$session = new Sessions();
$session->getTz();
```

in the case of the above example this would return Europe/London
