<h1 align="center">
Laravel Middlewarize
</h1>

<h2 align="center">
     :ribbon:
Extract extra fluffy code into middlewares
     :ribbon:
</h2>


<p align="center">
    <img width="300px" src="https://user-images.githubusercontent.com/6961695/63593162-a2fafe80-c5c8-11e9-8aba-f9f6aa298c25.png" alt="Onion"></img>
</p>

    
[![Maintainability](https://api.codeclimate.com/v1/badges/265609ba555d5fd06560/maintainability)](https://codeclimate.com/github/imanghafoori1/laravel-middlewarize/maintainability)
<a href="https://scrutinizer-ci.com/g/imanghafoori1/laravel-middlewarize"><img src="https://img.shields.io/scrutinizer/g/imanghafoori1/laravel-middlewarize.svg?style=flat-square" alt="Quality Score"></img></a>
[![Latest Stable Version](https://poser.pugx.org/imanghafoori/laravel-middlewarize/v/stable)](https://packagist.org/packages/imanghafoori/laravel-middlewarize)
[![Build Status](https://travis-ci.org/imanghafoori1/laravel-middlewarize.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-middlewarize)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-middlewarize/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-middlewarize/?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=round-square)](LICENSE.md)
</p>



### Installation: 

```
composer require imanghafoori/laravel-middlewarize
```

You can use middlewares to decorate any method calls on any object.

### How to use:

Put the `\Imanghafoori\Middlewarize\Middlewarable` trait on your class.

For example consider a simple repository class:

```php
class UserRepository
{
    use Middlewarable;     //   <----  Put middleware on class
    
    public function find($id) 
    {
        return User::find($id);   //   <----  we wanna cache it, right ?
    }
    ...
}

```

### Define a Middleware:

```php

class CacheMiddleware
{
    public function handle($data, $next, $key, $ttl)
    {
        // 1. This part runs before method call
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        
        $value = $next($data);  // <--- 2. Runs the actual method
        
       
        Cache::put($key, $value, $ttl);  // <-- 3. This part runs after method
        
        return $value;
    }
}
```

Since middlewares are resolved out of the laravel container, you can pass any abstract string as a middleware and bind it on the IOC:

```php
public function boot()
{
    app()->singleton('cacher', CacheMiddleware::class);  // <---- Optional step
}
```

### Use the Middleware:

Cleaned controller will look like this:
```php
public function show($id, UserRepository $repo)
{
    $cachedUser = $repo
        ->middleware('cacher:fooKey,60')
        ->find($id);
}
```
Easy Peasy Yeah ?!

You totally separate the cache concern into a new class.

So let's compare...

#### Before: 

Before utilizing middlewares our code was like this:

```php
public function show($id, UserRepository $repo)
{
    if (Cache::has('user.'.$id)) {
        return Cache::get('user.'.$id); // <--- extra fluff around ->find($id)
    }
        
    $value = $repo->find($id);  //   <--- important method call here.

    Cache::put('user.'.$id, $value, 60); // <--- extra fluff around ->find($id)
        
    return $value;
}
```

### Overriding default Middleware method:
```php
public function show($id, UserRepository $repo)
{
    $cachedUser = $repo
        ->middleware('cacher@MyHandle1:fooKey,60')  // <--- Overrides default "handle" method name
        ->find($id);
}
```

### Multiple middlewares:
```php
public function show($id, UserRepository $repo)
{
    $cachedUser = $repo->middleware(['middle1', 'middle2', 'middle3'])->find($id);
}
```

The order of execution is like that:
<p align="center">
   Start ===>  ( middle1 -> middle2 -> middle_3 (  <b> find </b> ) middle_3 -> middle2 -> middle1 )  ===> result !!!
</p>

### Middlewares on facades ?!

You wanna use facades to call the repo ?! No problem.
```php
$cachedUser = UserRepositoryFacade::middleware('cacher:fooKey,60 seconds')->find($id);
```

### Objects as middlewares:
You can also use objects as middlewares for more eloborated scenarios.
```php
$obj = new CacheMiddleware('myCacheKey', etc...);   //   <---- you send depedencies to it.

$repo->middleware($obj)->find($id);

```

### Middleware on static methods:
```php
User::find($id);       //  <--- Sample static method call

User::middlewared('cache:key,10')->find($id); // <--- you can have a decorated call

// also you must put 'middlewarable' trait on User model.
```

### Testing:
As we mentioned before middlewares are resolved out of the IOC, and that means you can easily swap them out while running your tests.

```php

class NullCacheMiddleware
{
    public function handle($data, $next, $key, $ttl)
    {
        return $next($data); // <--- this "null middleware" does nothing.
    }
}


public function testSomeThing()
{
    app()->singleton('cacher', NullCacheMiddleware::class);  // <--- this causes to replace the cache middleware
    
    $this->get('/home');
}

```

Here we have neutralized the middleware to do "nothing" while the tests are running.


### What happens if exception is thrown from your method?

It is important to know if you throw an exception in your method, the post middlewares still execute and the value of `$value = $next(data)` would be the thrown exception.
The exception is rethrown when all middlewares finished executing.


--------------------

### :raising_hand: Contributing:

If you find an issue, or have a better way to do something, feel free to open an issue or a pull request.


### :star: Your Stars Make Us Do More :star:

As always if you found this package useful and you want to encourage us to maintain and work on it. Just press the star button to declare your willing.

--------------------


## More from the author:

### Laravel Widgetize

 :gem: A minimal yet powerful package to give a better structure and caching opportunity for your laravel apps.

- https://github.com/imanghafoori1/laravel-widgetize

-------------------


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

----------------

<p align="center">
  
    Logic will get you from a to z, imagination will take you everywhere.
    
    "Albert Einstein"
    
</p>
