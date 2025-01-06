# PHP Custom Framework

Easy Framework.

PHP >= 5.6

DB Driver: mysqli

## Router

public/index.php

```php
$router->get('user', 'UserController', 'findAll');
$router->get('user/{id}', 'UserController', 'find');
$router->post('user', 'UserController', 'store');
$router->put('user/{id}', 'UserController', 'update');
$router->patch('user/{id}', 'UserController', 'updateField');
$router->delete('user/{id}', 'UserController', 'destroy');
```

## Controller

```php
$router->get('user/{id}/children/{name}', 'UserController', 'findChild');
```

```php
public function findChild(Request $request, $id, $name)
{
   // do something
}
```

## Middleware

```php
$router->get('user', 'UserController', 'findAll', [ExampleMiddleware::class]);
$router->get('user', 'UserController', 'findAll', [ExampleMiddleware::class, Example2Middleware::class]);
```

```php
public function handle($request)
{
    // do something

    return $request;
}
```

## Router Group

```php
$router->group('test', [TestMiddleware::class], function ($router) {
    $router->get('', 'HomeController', 'index');
});
```

## Request

```php
$request->all();
$request->body();
$request->body('name', 'default'); // Request Body
$request->query('name', 'default'); // ?name=123456
$request->files('file');
$request->text('text', '');
$request->headers('Authorization');
$request->bearerToken();
```

## Response

```php
$this->response->json($output); // 200
$this->response->json(['success' => false, 'message' => 'error'], 500);
$this->response->error('Unauthorized', 401);
```

## Guard

```php
$guard = new Guard('admin'); // Session
$guard->id = 1;
$guard->name = 'Alice';
```

```php
$admin = $this->getGuard('admin');  // Session
echo $admin['id'];
echo $admin['name'];
```
