<?php



$router->get('/profile', 'ProfileController@index');


$router->get('/profile/users', 'ProfileController@getUsers');
$router->get('/profile/{id}', 'ProfileController@getUserById');



$router->post('/profile/create', 'ProfileController@createUser');
$router->post('/profile/login', 'ProfileController@login');

$router->put('/profile/update', 'ProfileController@updateUser');

$router->post('/profile/setPasswordApplyByPhone', 'ProfileController@setPasswordApplyByPhone');
$router->post('/profile/setPasswordApplyByEmail', 'ProfileController@setPasswordApplyByEmail');

$router->post('/profile/addEmailToUser', 'ProfileController@addEmailToUser');
$router->post('/profile/activateEmail', 'ProfileController@activateEmail');
$router->post('/profile/generateEmailResetHash', 'ProfileController@generateEmailResetHash');

$router->post('/profile/addPhoneToUser', 'ProfileController@addPhoneToUser');
$router->post('/profile/activatePhone', 'ProfileController@activatePhone');
$router->post('/profile/generatePhoneResetHash', 'ProfileController@generatePhoneResetHash');

$router->post('/profile/deleteUserByPhone', 'ProfileController@deleteUserByPhone');

$router->post('/profile/setPushToken', 'ProfileController@setPushToken');

$router->post('/profile/create/promo', 'ProfileController@createUserFromPromo');

//******
//short mobile creation
//
$router->post('/profile/m/create', 'ProfileController@mobileCreateUser');

$router->post('/profile/m/registration', 'ProfileController@mobileRegistartionUser');

$router->post('/profile/m/confirmTerm', 'ProfileController@mobileConfirmTerm');

$router->post('/profile/m/smsCode', 'ProfileController@getSmsCode');

$router->post('/profile/m/login', 'ProfileController@mobileLogin');

