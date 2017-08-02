<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/../views/');
$twig = new Twig_Environment($twigLoader, [
    //'cache' => '/tmp/twigCache'
]);

$mailerTransport = (new Swift_SmtpTransport(getenv('MAILER_SERVER'), getenv('MAILER_PORT')))
    ->setUsername(getenv('MAILER_USERNAME'))
    ->setPassword(getenv('MAILER_PASSWORD'));
$mailer = new Swift_Mailer($mailerTransport);

$contactMailValidator = new \Particle\Validator\Validator();
$contactMailValidator->required('name')->string();
$contactMailValidator->required('email')->email();
$contactMailValidator->required('message')->string();
