<?php

class Crud
{
  public $connection;
  public $base_dir;
  public $columns;
  public $tablename;

  function __construct($connection, $base_dir, $columns, $tablename)
  {
    $this->base_dir = $base_dir;
    $this->connection = $connection;
    $this->columns = $columns;
    $this->tablename = $tablename;
    // this is an orm file, not a api route
  }

  // GET
  public function index()
  {
    $response = [];
    $sql = "select * from $this->tablename";
    $result = $this->connection->query($sql);
    if ($result) {
      while($row = $result->fetch_assoc()) {
        $response[] = $row;
      }
      $this->display_result($response);
    }
  }

  // POST
  public function create($args)
  {
    $cols = [];
    $values = [];

    $column_names_available = [];
    foreach( $this->columns as $col => $type ) {
      $column_names_available[] = $col;
    }

    // print_r($column_names_available);

    foreach( $_GET as $key => $value ) {
      if (in_array($key, $column_names_available)) {
        $cols[] = "`" . mysqli_real_escape_string($this->connection,$key) . "`";
        $values[] = "'" . mysqli_real_escape_string($this->connection,$value) . "'";
      }
    }
    $cols = implode(',', $cols);
    $values = implode(',', $values);
    $sql = "INSERT INTO `$this->tablename` ($cols) VALUES ($values)";
    $result = $this->connection->query($sql);
    if ($result) {
      $this->display_result($this->connection->insert_id);
    }
  }

  // GET
  public function read()
  {
    $response = [];
    $sql = "select * from $this->tablename where id = " . $_GET['id'];
    $result = $this->connection->query($sql);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $response[] = $row;
      }
      $this->display_result($response);
    } else {
      $this->display_result(0);
    }

  }

  // POST
  public function update()
  {
    $sql = "UPDATE $this->tablename SET lastname='Doe' WHERE id=2";
  }

  // DELETE
  public function delete()
  {
    if (isset($_GET['id']) && isset($_GET['delete'])) {
      $id = $_GET['id'];
      $sql = "DELETE FROM $this->tablename WHERE id = $id";
      $result = $this->connection->query($sql);
      if ($result) {
        $this->display_result('deleted ' . $id);
      }
    }
  }

  public function display_result($arg)
  {
    if (is_array($arg)) {
      echo json_encode($arg);
    } else {
      echo json_encode(array(
        'result' => $arg
      ));
    }

  }

}


 ?>
