 <?php

$router->get('', 'PagesController@home');

$router->get('expressions', 'ExpressionsController@index');
$router->post('expressions', 'ExpressionsController@store');
$router->get('expressions/{int:id}/edit', 'ExpressionsController@edit');
$router->get('expressions/{int:id}/delete', 'ExpressionsController@delete');

 /**
  * API routes
  */
$router->get('api/expressions', 'Api\ExpressionsController@index');
$router->post('api/expressions', 'Api\ExpressionsController@store');
$router->get('api/expressions/{int:id}/edit', 'Api\ExpressionsController@edit');
$router->get('api/expressions/{int:id}/delete', 'Api\ExpressionsController@delete');
$router->get('api/expressions/{int:id}/view', 'Api\ExpressionsController@view');