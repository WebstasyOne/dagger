<?php
  /*This script is written focussing on the execution efficiency rather than coding standards, readability and maintainability*/
  require_once 'config.php';

  $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE_NAME);

  $method = $_SERVER['REQUEST_METHOD'];

  function buildInsertQuery($dbTable, $params){
    return 'INSERT into ' . $dbTable .'(' . $params[0] . ') VALUES(' . $params[1] . ')';
  }

  function processParams($arr){
    $keys = array();
    $values = array();
    foreach ($arr as $key => $value) {
      array_push($keys, $key);
      /*If the value is a String without quotes add the quotes*/
      if(is_string($value) && $value[0] != '\'' && $value[0] != '\"'){
        $value = '\'' . $value . '\'';
      }
      array_push($values, $value);
    }
    return array(implode($keys, ','), implode($values, ','));
  }
  $params = '';
  switch ($method) {
  case 'PUT':
    echo 'EDIT';
    break;
  case 'POST':
    echo 'CREATE';
    $params = processParams($_POST);
    break;
  case 'GET':
    echo 'VIEW';
    $params = processParams($_GET);
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
  $conn->query(buildInsertQuery('test', $params));
  echo mysqli_error($conn);
  $conn->close();
?>
