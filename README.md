

<h1> Laravel Middlewarize </h1>
<h2> Decorator your method calls in laravel </h2>

    
[![Maintainability](https://api.codeclimate.com/v1/badges/265609ba555d5fd06560/maintainability)](https://codeclimate.com/github/imanghafoori1/laravel-middlewarize/maintainability)
<a href="https://scrutinizer-ci.com/g/imanghafoori1/laravel-middlewarize"><img src="https://img.shields.io/scrutinizer/g/imanghafoori1/laravel-middlewarize.svg?style=flat-square" alt="Quality Score"></img></a>
[![Latest Stable Version](https://poser.pugx.org/imanghafoori/laravel-middlewarize/v/stable)](https://packagist.org/packages/imanghafoori/laravel-middlewarize)
[![Build Status](https://travis-ci.org/imanghafoori1/laravel-middlewarize.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-middlewarize)
[![Monthly Downloads](https://poser.pugx.org/imanghafoori/laravel-middlewarize/d/monthly)](https://packagist.org/packages/imanghafoori/laravel-middlewarize)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-middlewarize/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-middlewarize/?branch=master)
</p>



### Installation : 

```
composer require imanghafoori/laravel-middlewarize
```

You can use middlewares to decorate any method calls on any object.

### Use Cases:

First of all, You should use the `\Imanghafoori\Middlewarize\Middlewarable` trait on your class.

For example consider a repository class:

```php
class UserRepository
{
    use Middlewarable;
    
    public function find ($id) 
    {
        return User::find($id);   ///    <----  we wanna cache it, right ?
    }
    ...
}

```

What would you do ?!

Put cache logic in repo class? No, no. Put it in your call site ? Ugly.


You define a middleware to wrap around the method you are calling :

```php
class CacheMiddleware
{
    public function handle($data, $next, $key, $ttl)
    {
        
        if(\Cache::has($key)) {
            return \Cache::get($key);
        }
       
        $value = $next($data);
        
        \Cache::put($key, $value, $ttl);
        
        return $value;
    }
}
```

Then you can alias you middleware like any other class, in the config/app.php
```php
'cacher' => App\Middlewares\CacheMiddleware,
```

Now it is ready to:

```php

public function show($id, UserRepository $repo)
{
    $cachedUser = $repo->middleware('cacher:fooKey,60 seconds')->find($id);
}

```

Easy Peasy Yeah ?!

Middlwares can come from packages, there is no a need for you to always define them.

You wanna use facades to call the repo ?!

```php

$cachedUser = UserRepositoryFacade::middleware('cacher:fooKey,60 seconds')->find($id);

```

You can also use objects as middlewares for more eloborated scnarios.


#### Objects as middlewares
```php

$object = new CacheMiddleware(...);   //   <----- you send depedencies to it.

$repo->middleware($object)->find($id);

```

#### Wrapping static methods:

```php
User::middlewared('...')->find($id);  //   <----- Here we are directly call it through an eloquent model.
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
