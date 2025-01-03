# PHP Custom Framework

PHP >= 5.6

## Router

public/index.php

```php
$router->get('user', 'UserController', 'findAll');
$router->get('user/{id}', 'UserController', 'findById');
$router->post('user', 'UserController', 'store');
$router->put('user/{id}', 'UserController', 'update');
$router->patch('user/{id}', 'UserController', 'updateField');
$router->delete('user/{id}', 'UserController', 'destroy');
```

```php
$router->get('user/{id}/children/{name}', 'UserController', 'findChild');
```

## Middleware

```php
$router->get('user', 'UserController', 'findAll', [ExampleMiddleware::class]);
$router->get('user', 'UserController', 'findAll', [ExampleMiddleware::class, Example2Middleware::class]);
```

## Request

```php
$request->all();
$request->body();
$request->body('name', 'default');
$request->query('name', 'default');
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

$this->response->no('message', 500);
$this->response->ok('message', 200, $output);
```

## Guard

```php
$guard = new Guard('admin');
$guard->id = 1;
$guard->name = 'Alice';
```

```php
$admin = $this->getGuard('admin');
echo $admin['id']; // 1
echo $admin['name']; // Alice
```
