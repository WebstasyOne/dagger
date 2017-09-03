<?php
  require_once 'config.php';

  $method = $_SERVER['REQUEST_METHOD'];
  $request = explode("/", substr($_SERVER['PATH_INFO']));

  switch ($method) {
  case 'PUT':
    echo 'EDIT';
    break;
  case 'POST':
    echo 'CREATE';
    break;
  case 'GET':
    echo 'VIEW';
    echo $request[0];
    break;
  case 'DELETE':
    echo 'DELETE';
    break;
  case 'HEAD':
    echo 'E';
    break;
  case 'OPTIONS':
    echo 'E';
    break;
  default:
    handle_error($request);
    break;
  }
?>
