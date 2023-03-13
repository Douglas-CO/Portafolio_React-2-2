<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");


include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();
 
$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET":
        $sql = "SELECT * FROM musicas";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $musicas = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $musicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
 
        echo json_encode($musicas);
        break;
    case "POST":
        $musica = json_decode( file_get_contents('php://input') );
        $sql = "INSERT INTO musicas(id, name, descripcion, created_at) VALUES(null, :name, :descripcion, :created_at)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':name', $musica->name);
        $stmt->bindParam(':descripcion', $musica->descripcion);
        $stmt->bindParam(':created_at', $created_at);
 
        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        echo json_encode($response);
        break;
 
    case "PUT":
        $musica = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE musicas SET name= :name, descripcion =:descripcion, updated_at =:updated_at WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':id', $musica->id);
        $stmt->bindParam(':name', $musica->name);
        $stmt->bindParam(':descripcion', $musica->descripcion);
        $stmt->bindParam(':updated_at', $updated_at);


        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($response);
        break;
 
 
    case "DELETE":
        $sql = "DELETE FROM musicas WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);
 
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[3]);
 
        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($response);
        break;
    }
?>