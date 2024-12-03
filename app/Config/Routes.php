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

