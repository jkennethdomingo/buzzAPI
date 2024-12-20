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



$routes->group('v2', function ($routes) {
    $routes->get('dropdown', 'BuzzV2Controller::getSectionGrouping');
    $routes->get('state', 'BuzzV2Controller::getBuzzerState');
    $routes->post('login', 'BuzzV2Controller::login');
    $routes->post('buzz', 'BuzzV2Controller::buzz');
    $routes->get('list/(:segment)', 'BuzzV2Controller::getStudentsBySection/$1');
    $routes->get('section/(:segment)', 'BuzzV2Controller::getSectionNameById/$1');
    $routes->post('award', 'BuzzV2Controller::awardScore');
    $routes->post('reset', 'BuzzV2Controller::resetBuzzerState');
    $routes->post('logout', 'BuzzV2Controller::logout');
});

$routes->group('v3', function ($routes) {
    $routes->post('login', 'BuzzV3Controller::login');
    $routes->get('dropdown', 'BuzzV3Controller::getSectionGrouping');
    $routes->get('list/(:segment)', 'BuzzV3Controller::getStudentsBySection/$1');
    $routes->post('reset', 'BuzzV3Controller::resetBuzzerState');
    $routes->post('logout', 'BuzzV3Controller::logout');
    $routes->post('buzz', 'BuzzV3Controller::buzz');
    $routes->post('logout-all', 'BuzzV3Controller::logoutAllPlayers');
    $routes->get('files', 'BuzzV3Controller::serveFile');
    $routes->post('award', 'BuzzV3Controller::awardScore');

    // Activities
    $routes->get('activities', 'BuzzV3Controller::fetchActivities');
    $routes->get('user-activities/(:num)', 'BuzzV3Controller::getUserActivities/$1');
    $routes->post('mark-as-done', 'BuzzV3Controller::markAsDoneAnActivity');
    $routes->post('unmark-as-done', 'BuzzV3Controller::unMarkAsDoneAnActivity');
    $routes->post('help', 'BuzzV3Controller::requestForHelp');
    $routes->post('cancel-help', 'BuzzV3Controller::cancelRequestForHelp');
    $routes->post('give-score', 'BuzzV3Controller::scoreAnActivity');
});

$routes->group('raffle', function ($routes) {
    // Route to get participants
    $routes->get('participants', 'RaffleController::getParticipants');
    $routes->post('add-participant', 'RaffleController::addParticipant');
    $routes->put('raffle/edit-participant/(:segment)', 'RaffleController::editParticipant/$1');
    $routes->delete('raffle/delete-participant/(:segment)', 'RaffleController::deleteParticipant/$1');
    $routes->put('raffle/mark-as-winner/(:segment)', 'RaffleController::markAsWinner/$1');
});

