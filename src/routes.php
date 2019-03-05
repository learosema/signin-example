<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Session:
@session_start();

// Routes

$app->get('/', function (Request $request, Response $response) {
  // Sample log message
  $this->logger->info("Slim-Skeleton '/' route");
  // Render index view
  $logged_in = array_key_exists('user', $_SESSION) && $_SESSION['user'] !== null;
  $user = $logged_in ? $_SESSION['user'] : null;
  return $this->renderer->render($response, 'index.phtml', [
    'logged_in' => $logged_in,
    'user'      => $user,
    'client_id' => $this->settings["clientId"]
  ]);
});

$app->get('/auth', function (Request $request, Response $response) {
  $query = $request->getQueryParams();
  $code = $query["code"];
  $this->logger->info(
    json_encode([
      'client_id' => $this->settings['clientId'],
      'client_secret' => $this->settings['clientSecret'],
      'code' => $code,
      'redirect_uri' => 'https://localhost:8080/'
    ])
  );
  $http = new GuzzleHttp\Client([
    'headers' => ['Accept' => 'application/json']
  ]);
  $authResponse = $http->request(
    'POST',
    'https://github.com/login/oauth/access_token',
    [
      'json' => [
        'client_id' => $this->settings['clientId'],
        'client_secret' => $this->settings['clientSecret'],
        'code' => $code
      ]
    ]
  );
  $authData = json_decode($authResponse->getBody());
  $accessToken = $authData->access_token;
  $tokenType = $authData->token_type;

  $http = new GuzzleHttp\Client([
    'headers' => [
      'Accept' => 'application/json',
      'Authorization' => 'token ' . $accessToken
    ]
  ]);

  $userResponse = $http->request('GET', 'https://api.github.com/user');
  $user = json_decode($userResponse->getBody());
  $_SESSION['user'] = $user;

  return $response->withRedirect('/');
});


$app->get('/logout', function (Request $request, Response $response) { 
  $_SESSION['user'] = null;
  session_destroy();
  return $response->withRedirect('/');
});