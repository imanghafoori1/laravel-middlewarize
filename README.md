# Laravel Middlewarize

## Decorator pattern in laravel


### Installation : 

```
composer require imanghafoori/laravel-middlewarize
```

You can use middlewares to decorate any method calls on any object.

### Use Cases:

For better syntax, you better use the `Middlewarable` trait on your class.

For example a repository class :

```php
class UserRepository
{
    use Middlewarable;
    
    public function find ($id) 
    {
        return User::find($id)
    }
    ...
}

```

Now you can put middlewares to wrap your method calls:

```php

public function show($id, UserRepository $repo)
{
    $cachedUser = $repo->middleware('cacher:fooKey,60 seconds')->find($id);
}

```

Easy Peasy Yeah ?!

You wanna use facades to call the repo ?!

```

$cachedUser = UserRepositoryFacade::middleware('cacher:fooKey,60 seconds')->find($id);

```

--------------------

### :raising_hand: Contributing 
If you find an issue, or have a better way to do something, feel free to open an issue or a pull request.
If you use laravel-widgetize in your open source project, create a pull request to provide it's url as a sample application in the README.md file. 


### :exclamation: Security
If you discover any security related issues, please use the `security tab` instead of using the issue tracker.


### :star: Your Stars Make Us Do More :star:
As always if you found this package useful and you want to encourage us to maintain and work on it. Just press the star button to declare your willing.



### More from the authors:


### Laravel HeyMan

:gem: It allows to write expressive code to authorize, validate and authenticate.

- https://github.com/imanghafoori1/laravel-heyman


------------

### Laravel Terminator


 :gem: A minimal yet powerful package to give you opportunity to refactor your controllers.

- https://github.com/imanghafoori1/laravel-terminator


------------

### Laravel AnyPass

:gem: It allows you login with any password in local environment only.

- https://github.com/imanghafoori1/laravel-anypass

------------

### Eloquent Relativity

:gem: It allows you to decouple your eloquent models to reach a modular structure

- https://github.com/imanghafoori1/eloquent-relativity
