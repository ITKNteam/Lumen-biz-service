<?php

$router->group(['prefix' => '/profile'], function () use ($router) {
    $router->get('', 'ProfileController@index');

    $router->get('/users', 'ProfileController@getUsers');
    $router->get('/{id}', 'ProfileController@getUserById');

    $router->post('/create', 'ProfileController@createUser');
    $router->post('/login', 'ProfileController@login');

    $router->put('/update', 'ProfileController@updateUser');

    $router->post('/setPasswordApplyByPhone', 'ProfileController@setPasswordApplyByPhone');
    $router->post('/setPasswordApplyByEmail', 'ProfileController@setPasswordApplyByEmail');

    $router->post('/addEmailToUser', 'ProfileController@addEmailToUser');
    $router->post('/activateEmail', 'ProfileController@activateEmail');
    $router->post('/generateEmailResetHash', 'ProfileController@generateEmailResetHash');

    $router->post('/addPhoneToUser', 'ProfileController@addPhoneToUser');
    $router->post('/activatePhone', 'ProfileController@activatePhone');
    $router->post('/generatePhoneResetHash', 'ProfileController@generatePhoneResetHash');

    $router->post('/deleteUserByPhone', 'ProfileController@deleteUserByPhone');

    $router->post('/setPushToken', 'ProfileController@setPushToken');

    $router->post('/create/promo', 'ProfileController@createUserFromPromo');

//******
//short mobile creation
//
    $router->post('/m/create', 'ProfileController@mobileCreateUser');

    $router->post('/m/registration', 'ProfileController@mobileRegistartionUser');

    $router->post('/m/confirmTerm', 'ProfileController@mobileConfirmTerm');

    $router->post('/m/smsCode', 'ProfileController@getSmsCode');

    $router->post('/m/login', 'ProfileController@mobileLogin');
});






