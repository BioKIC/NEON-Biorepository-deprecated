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

$router->get('/v2', function () use ($router) {
	return $router->app->version();
});

$router->group(['prefix' => 'v2'], function () use ($router) {

	$router->get('collection/{id}', ['uses' => 'CollectionController@showOneCollection']);
	$router->get('collection',  ['uses' => 'CollectionController@showAllCollections']);

	$router->get('occurrence/{id}', ['uses' => 'OccurrenceController@showOneOccurrence']);
	$router->get('occurrence',  ['uses' => 'OccurrenceController@showAllOccurrences']);
	$router->get('occurrence/{id}/media', ['uses' => 'OccurrenceController@showOneOccurrenceMedia']);
	$router->get('occurrence/{id}/identification', ['uses' => 'OccurrenceController@showOneOccurrenceIdentifications']);

	$router->get('installation',  ['uses' => 'InstallationController@showAllPortals']);
	$router->get('installation/ping', ['uses' => 'InstallationController@pingPortal']);
	$router->get('installation/{id}', ['uses' => 'InstallationController@showOnePortal']);
	$router->get('installation/{id}/touch',  ['uses' => 'InstallationController@portalHandshake']);
	$router->get('installation/{id}/occurrence',  ['uses' => 'InstallationController@showOccurrences']);

	$router->get('inventory/{id}', ['uses' => 'InventoryController@showOneInventory']);
	$router->get('inventory',  ['uses' => 'InventoryController@showAllInventories']);
	$router->get('inventory/{id}/taxa', ['uses' => 'InventoryController@showOneInventoryTaxa']);

	$router->get('media',  ['uses' => 'MediaController@showAllMedia']);
	$router->get('media/{id}', ['uses' => 'MediaController@showOneMedia']);
	$router->post('media', ['uses' => 'MediaController@create']);
	$router->delete('media/{id}', ['uses' => 'MediaController@delete']);
	$router->put('media/{id}', ['uses' => 'MediaController@update']);

	//$router->get('taxonomy',  ['uses' => 'TaxonomyController@showAllTaxa']);
	//$router->get('taxonomy/{id}', ['uses' => 'TaxonomyController@showOneTaxon']);
});
