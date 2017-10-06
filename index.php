<?php
  /*This script is written focussing on the performance rather than coding standards, readability and maintainability*/
  require_once 'config.php';

  $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE_NAME);

  $method = $_SERVER['REQUEST_METHOD'];

  /*Functions*/
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
  function buildDeleteQuery($dbTable, $id){
    global $conn;
    $rs = $conn->query("SHOW INDEX FROM $dbTable WHERE Key_name = 'PRIMARY'");
    return 'DELETE FROM ' . $dbTable . ' WHERE ' . $rs->fetch_assoc()['Column_name'] . '=' . $id;
  }

  function processParams($arr){
    $arr = array($arr);
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

  function getAllTableRecords($dbTable){
    global $conn;
    return mysqli_fetch_all($conn->query("SELECT * FROM " . $dbTable));
  }

  function getTableRecord($dbTable, $id){
    global $conn;
    $rs = $conn->query("SHOW INDEX FROM $dbTable WHERE Key_name = 'PRIMARY'");
    if(is_string($id) && $id[0] != '\'' && $id[0] != '\"'){
      $id = '\'' . $id . '\'';
    }
    return mysqli_fetch_all($conn->query("SELECT * FROM " . $dbTable . ' WHERE ' . $rs->fetch_assoc()['Column_name'] . '=' . $id));
  }
  /*Processing*/
  $table = $_GET['table'];
  $id;
  if(isset($_GET['id'])){
    $id = $_GET['id'];
  }
  $params = '';
  switch ($method) {
  case 'POST':
    $params = processParams($_POST);
    $conn->query(buildInsertQuery($table, $params));
    break;
  case 'GET':
    header('Content-Type: application/json');
    $params = processParams($_GET);
    echo json_encode(isset($id) ? getTableRecord($table, $id) : getAllTableRecords($table));
    break;
  case 'PUT':
    $params = processParams(json_decode(file_get_contents("php://input")), true);
    $conn->query(buildUpdateQuery($table, $params, $_GET['id']));
    break;
  case 'DELETE':
    header('Content-Type: application/json');
    echo json_encode($conn->query(buildDeleteQuery($table, $_GET['id'])));
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
