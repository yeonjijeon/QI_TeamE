<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------


// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Flash messages
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------

$container['db'] = function ($c) {
    $db = $c['settings']['dbSettings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
// doctrine EntityManager
$container['em'] = function ($c) {
    $settings = $c->get('settings');
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        $settings['doctrine']['meta']['entity_path'],
        $settings['doctrine']['meta']['auto_generate_proxies'],
        $settings['doctrine']['meta']['proxy_dir'],
        $settings['doctrine']['meta']['cache'],
        false
    );
    return \Doctrine\ORM\EntityManager::create($settings['doctrine']['connection'], $config);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
    return $logger;
};




// ------controller-----------------------------------------------------------------------

$container['App\Controller\HomeController'] = function ($c) {
    return new App\Controller\HomeController($c);
};


$container['App\Controller\UserController'] = function ($c) {
    $logger = $c->get('logger');
    $userModel = $c->get('userModel');
    $view = $c->get('view');
    return new App\Controller\UserController($logger, $userModel,$view);
};


$container['App\Controller\AppController'] = function ($c) {
    $logger = $c->get('logger');
    $view = $c->get('view');
    $userModel = $c->get('userModel');

    return new App\Controller\AppController($view, $logger, $userModel);
};

$container['App\Controller\SensorController'] = function ($container) {
    $logger = $container->get('logger');
    $sensorModel = $container->get('sensorModel');
    $view = $container->get('view');

    return new App\Controller\SensorController( $logger, $sensorModel,$view);
};

$container['App\Controller\DataController'] = function ($container) {
    $logger = $container->get('logger');
    $dataModel = $container->get('dataModel');
    $view = $container->get('view');

    return new App\Controller\DataController($view, $dataModel,$logger);
};


// ------model-----------------------------------------------------------------------

$container['userModel'] = function ($c) {
    $settings = $c->get('settings');
    $userModel = new App\Model\UserModel($c->get('db'));
    return $userModel;
};

$container['sensorModel'] = function ($container) {
    $settings = $container->get('settings');
    $sensorModel = new App\Model\SensorModel($container->get('db'));
    return $sensorModel;
};

$container['dataModel'] = function ($container) {
    $settings = $container->get('settings');
    $dataModel = new App\Model\DataModel($container->get('db'));
    return $dataModel;
};





