<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/create', 'BuzzController::createUser');
$routes->get('/buzzer-state', 'BuzzController::getBuzzerState');
$routes->post('/press', 'BuzzController::pressBuzzer');
$routes->post('/award', 'BuzzController::awardScore');
$routes->get('/students/section/(:segment)', 'BuzzController::getAllStudents/$1');



$routes->group('v2', function($routes) {
    $routes->get('dropdown', 'BuzzV2Controller::getSectionGrouping');
    $routes->post('login', 'BuzzV2Controller::login');
    $routes->post('buzz', 'BuzzV2Controller::buzz');
    $routes->get('list/(:segment)', 'BuzzV2Controller::getStudentsBySection/$1');
    $routes->get('award', 'BuzzV2Controller::awardScore');
});

