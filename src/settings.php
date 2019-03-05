<?php
use Symfony\Component\Dotenv\Dotenv;

$env = new Dotenv();
$env->load(__DIR__ . '/../.env');

return [
  'settings' => [
    'clientId' => $_ENV['CLIENT_ID'],
    'clientSecret' => $_ENV['CLIENT_SECRET'],
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    // Renderer settings
    'renderer' => [
      'template_path' => __DIR__ . '/../templates/'
    ],

    // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'path' => isset($_ENV['docker'])
        ? 'php://stdout'
        : __DIR__ . '/../logs/app.log',
      'level' => \Monolog\Logger::DEBUG
    ]
  ]
];
