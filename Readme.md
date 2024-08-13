# Lazarus Php Session Manager.

## what is the session manager?

The Lazarus php Session is a database Driven Session Handler and is used to make Sessions last Longer.

## Who is this read me for

This readme is for users who wish to install this package into there own existing script.

## Whats included

- Sessionmanager
- DatabaseManager.

## How to Install

it is recommended to use composer to install this package.

```
composer require lazarusphp/sessionmanager
```

## Before We begin !
As stated Above it uses a database to store the sessions, this requires another dependency called DatabaseManager which is bundled in when you install the script.

if you are comfortable modifying the script feel free to do so, other than that  Please read how to Configure the Database Class

The installtion and Instantiation Setup are for user wishing to integrate the script into there own system, everything else applies to the framework.

## Setting up the config file.

Create your Config FIle and put it on your server (supported Config files are as Follows )

* .php
* .ini
* .env

for this example we will use a php file.

```
$type = "";
$hostname = "";
$username = "";
$password = "";
$dbname = "";

```
 create your config.php file and fill in the details as this is a pdo based Connection $type is the Driver type, in this instance mysql

 Please look up pdo driver compatibilty list for more information. 

 ```
use LazarusPhp\DatabaseManager\CredentialsManager;

CredentialsManager::SetConfig("Path/to/your/config.php");
 ```



## How to Instantiate the Script

in order to install this script simply add the class namespace to your entrypoint file.

we will be using the Built in Database Manager for this section

```
use Lazarusphp\SessionManager;
use LazarusPhp\DatabaseManager\CredentialsManager;

CredentialsManager::SetConfig("Path/to/your/config.php");
   $sessions = new Session();
   if (session_status() == PHP_SESSION_NONE)
   {
        $session->start();
   }

```
that is the instantiation part set up now we can create read and destroy sessions with ease.


## How to create Sessions
Creating sessions can be done one of two ways, the first is the standard Superglobal method 

```
$_SESSION['username'] = "demo";
```
the second is the magic method, This method requires creating a new instance if you are not calling from a new page.

if you are calling from a different file or page do the following

```
$session = new Session();
$session->username = "demo";
```

## Reading Sessions

Like Above sessions can be read using both the magic method and superglobal method ways.

```
echo $_SESSION['username'];
```
```
$session = new Session();
echo $session["username"];
```
## Destroying sessions
Destroying sessions or unsetting sessions is away of getting rid of sessions and there values, normally used for logging out of a website.

```
//This destroys all sessions
session_destroy()

//you can also use unset 
unset($_SESSION["username"]);
```

using session destroy is quicker that manually destroying one by one, use unset if you only need to destroy one session at a time.

## Garbage collection
Another feature build into the Session manager is Garbage collection, this is a built in feature but this allows the user to clear out Expired Cookies from the database.

To do this use the following command.

```
session_gc();
```

You should only need to Call a new instance once per file or page.

if this script is being used as part of the framework the instantiation can be passed into the controller when using the Router system.

want to understand more about the DatabaseManager and how to use it as part of your own system [Click here](https://github.com/lazarusphpCore/DatabaseManager)


 
