<?php

use App\Infra\Routes\Router;

Router::post('/api/users', 'UserController@register');
Router::get('/api/users', 'UserController@list');
Router::get('/api/users/{id}', 'UserController@findById');
