<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('user/{id}', 'UserController@getName' );
$router->get('users/list/{num}',['middleware'=>['auth','permissions:listUsers'],'uses'=> 'UserController@listUsers']);
$router->get('users/list_all',['middleware'=>['auth','permissions:listUsers'],'uses'=> 'UserController@listUsers']);
// $router->get('makerole','UserController@makerole');
$router->delete('users/delete/{id}',['middleware'=>['auth','permissions:deleteUser'],'uses'=> 'UserController@deleteUser']);
$router->post('users/create',['middleware'=>['auth','permissions:addUser'],'uses'=> 'UserController@createUser']);


$router->post('login','AuthController@login');

$router->get('verify/{id}/otp/{otp}', 'AuthController@verifiymail');

$router->post('register', 'AuthController@register');
$router->post('me', ['middleware' => 'auth', 'uses' => 'AuthController@me']);

$router->get('forgottonpassword','ForgotController@sendOTP');
$router->post('forgottonpassword','ForgotController@setNewPassword');
