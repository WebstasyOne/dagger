<?php
  /*This script is written focussing on the performance rather than coding standards, readability and maintainability*/
  require_once 'config.php';

  $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE_NAME);

  $method = $_SERVER['REQUEST_METHOD'];

  function buildInsertQuery($dbTable, $params){
    return 'INSERT INTO ' . $dbTable .'(' . $params[0] . ') VALUES(' . $params[1] . ')';
  }
  function buildUpdateQuery($dbTable, $params, $id){
    global $conn;
    $rs = $conn->query("SHOW INDEX FROM $dbTable WHERE Key_name = 'PRIMARY'");
    $setList = array();
    foreach ($params as $key => $value) {
      /*If the value is a String without quotes add the quotes*/
      if(is_string($value) && $value[0] != '\'' && $value[0] != '\"'){
        $value = '\'' . $value . '\'';
      }
      $setQ = $key . '=' . $value;
      array_push($setList, $setQ);
    }
    return 'UPDATE ' . $dbTable . ' SET ' . implode($setList, ',') . ' WHERE ' . $rs->fetch_assoc()['Column_name'] . '=' . $id;

  }
  function buildDeleteQuery($dbTable, $params, $id){
    global $conn;
    $rs = $conn->query("SHOW INDEX FROM $dbTable WHERE Key_name = 'PRIMARY'");
    return 'DELETE FROM ' . $dbTable . ' WHERE ' . $rs->fetch_assoc()['Column_name'] . '=' . $id;
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
    $params = processParams($_PUT);
    $conn->query(buildUpdateQuery('test', $_PUT, 22));
    break;
  case 'POST':
    echo 'CREATE';
    $params = processParams($_POST);
    $conn->query(buildInsertQuery('test', $params));
    break;
  case 'GET':
    echo 'VIEW';
    $params = processParams($_GET);
    break;
  case 'DELETE':
    echo 'DELETE';
    $params = processParams($_DELETE);
    $conn->query(buildDeleteQuery('test', $params));
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
  echo mysqli_error($conn);
  $conn->close();
?>
