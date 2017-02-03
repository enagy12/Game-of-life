# PHP backend library

DOXA Soft &copy; 2017

## How to use

First, initialize a backend app configuration class with your configurations
with a finally called `ready()` function. It locks the configuration and allows
the app to run. Otherwise it will result in a `NoRequiredDataException`

```php
$config = new DoxaBackendConfiguration();
$config->frontend('http://myfrontend.com');
$config->backend('http://be.myfrontend.com');
$config->base('myapibase');
$config->db_host('mydbhost');
$config->db_name('mydbname');
$config->db_user('mydbuser');
$config->db_pass('password');
$config->ready();

// You can simply pass everything to the constructor in this order:
// frontend, backend, base, db_host, db_name, db_user, db_pass
$config = new DoxaBackendConfiguration(
    'http://myfrontend.com',
    'http://be.myfrontend.com',
    'myapibase',
    'mydbhost',
    'mydbname',
    'mydbuser',
    'password'
);
$config->ready();
```

Then initialize a backend app class with that configuration class

```php
$app = new DoxaBackendApp($config);
```

The setters can be chained and the `ready()` function as well so you can simply do:

```php
$app = new DoxaBackendApp((new DoxaBackendConfiguration())
    ->frontend('http://myfrontend.com')
    ->backend('http://be.myfrontend.com')
    ->base('myapibase')
    ->db_host('mydbhost')
    ->db_name('mydbname')
    ->db_user('mydbuser')
    ->db_pass('password')
    ->ready()
);
```

After that you can register any number of request handlers with unique paths.
The second parameter is a class which has to extend the abstract class
`RequestHandler`.

```php
$app->addService('authentication', AuthenticationRS::class);
```

When you are done, call the run function of the backend app class.
```php
$app->run();
```

For example our backend will react like this now:

```
-> GET /hello/world
<- 404 NOT FOUND
Not a dedicated API url!

-> GET /myapibase/some/not/existing
<- 400 BAD REQUEST
Bad request!

-> POST /myapibase/authentication/login
{userName:user, password:pass}
<- 200 OK
// JWT token
```
