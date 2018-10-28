<?php
ini_set('display_errors', true); // remove this line in production environment
error_reporting(E_ALL | E_STRICT);

session_name('X4FPL');
session_start();
//session_cache_limiter('nocache');

/**
 * --------------------------------------------------------------
 * define system paths
 * --------------------------------------------------------------
 **/

// path to the folder which contains everything
define('BASE_PATH', __DIR__.'/..');
// path to the site folder
define('SITE_BASE', __DIR__.'/..');
// path to the haplo-framework files
define('HAPLO_FRAMEWORK_BASE', BASE_PATH.'/haplo-framework');
// path to the folder that contains config ini files
define('HAPLO_CONFIG_PATH', SITE_BASE.'/config');


/**
 * --------------------------------------------------------------
 * include haplo framework files
 * --------------------------------------------------------------
 **/
require HAPLO_FRAMEWORK_BASE.'/haplo-init.inc.php';

/**
 * --------------------------------------------------------------
 * include custom files
 * --------------------------------------------------------------
 **/

 require '../includes/app.inc.php';
 require '../includes/auth.inc.php';

 /**
  * --------------------------------------------------------------
  * set up URL mappings
  * --------------------------------------------------------------
  **/
$urls = array(
    // new stats api
    '/api/v1/get-something.json'                                                         => 'api/v1/get-something',
    
    
    // redirect URLs without trailing slash
    '/(?<path>[^\?]+[^/|\?])'                                                               => array(
                                                                                                'type' => 'redirect',
                                                                                                'url' => '/<path>/',
                                                                                                'code' => 301
                                                                                            ),
    
    
    
    
    '/'                                                                                     => 'home',
    
    
    
    // map everyting else to static-page action
    '/(?<template>[a-z0-9/-]*)'                                                             => 'static-page'
);

/**
 * --------------------------------------------------------------
 * create an instance of the router and pass in URL mappings
 * --------------------------------------------------------------
 **/
$router = HaploRouter::get_instance($urls);
$auth = new Auth();
$nonce = new HaploNonce($config->get_key('security', 'nonceSecret'));

/**
 * --------------------------------------------------------------
 * load selected action
 * --------------------------------------------------------------
 **/
if ($action = $router->get_action()) {
    require $action;

    if ($actionClass = $router->get_action_class()) {
        $actionClass::get_instance(
            // pass any other objects that need to be accessible to the
            // action class in this array
            array(
                'router' => $router,
                'auth' => $auth,
                'config' => $config, // global config object - instantiated in haplo-init.inc.php,
                'nonce' => $nonce
            )
        );
    } else {
        throw new HaploClassNotFoundException("Action class doesn't exist for action ".$action);
    }
}
