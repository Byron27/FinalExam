<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../db.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});
    $app->get('/student', function (Request $request, Response $response, array $args) {
    $db = new DB();
    $sql = "SELECT * FROM tblstudent";
  
    $connect = $db->connect();
  
    $result = mysqli_query($connect, $sql);


    if (mysqli_num_rows($result) > 0) {
      
        while($row = mysqli_fetch_all($result, MYSQLI_ASSOC)) {
            $response->getBody()->write(json_encode($row));
        }
    } 
    else {
        $response->getBody()->write("0 results");
    }
    mysqli_free_result($result);
    $db->closeConnection($connect);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/student/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $db = new DB();
    $sql = "SELECT * FROM tblstudent WHERE id = $id";
   
    $connect = $db->connect();
    
    $result = mysqli_query($connect, $sql);
    if (mysqli_num_rows($result) > 0) {
        
        while($row = mysqli_fetch_all($result, MYSQLI_ASSOC)) {
            $response->getBody()->write(json_encode($row));
        }
    } 
    else {
        $response->getBody()->write("0 results");
    }
    mysqli_free_result($result);
    $db->closeConnection($connect);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/student/add', function (Request $request, Response $response, array $args) {
    $data = $request->getBody();
    $value = json_decode($data, TRUE); 

    $db = new DB();
    $sql = "INSERT INTO tblstudent (first_name, last_name, birth_date) VALUES (?,?,?)";
    
    $connect = $db->connect();
    
    if($stmt = mysqli_prepare($connect, $sql)){
        mysqli_stmt_bind_param($stmt, "sss", $first_name, $last_name, $birth_date);
        
        $first_name = $value['first_name'];  
        $last_name = $value['last_name']; 
        $birth_date =  $value['birth_date'];      
        mysqli_stmt_execute($stmt);
        $response->getBody()->write("Record Added Successfully");
    }
    else{
        $response->getBody()->write("Error: Could not prepare query");
    }
    
    mysqli_stmt_close($stmt);
    $db->closeConnection($connect);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/student/edit/{id}', function (Request $request, Response $response, array $args) {
    $sql = null;

    $id = $request->getAttribute('id');   
    $data = $request->getBody();
    $value = json_decode($data, TRUE);

    if(!(empty($id))){
        $first_name = $value['first_name'];  
        $last_name = $value['last_name']; 
        $birth_date =  $value['birth_date'];      
        $sql = "UPDATE tblstudent SET first_name = '$first_name', last_name = '$last_name', birth_date ='$birth_date' WHERE id = " . $id;
    }
    else{
        die("Error: ID not Define");
    }

    $db = new DB();
    $connect = $db->connect();
    if (mysqli_query($connect, $sql)) {
        $response->getBody()->write("Record Update Successfully");
    } 
    else {
        $response->getBody()->write("Error: Update Record");
    }

    $db->closeConnection($connect);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->delete('/student/delete/{id}', function (Request $request, Response $response, array $args) {
    $sql = null;

    $id = $request->getAttribute('id');   
    $data = $request->getBody();
    $value = json_decode($data, TRUE);

    if(!(empty($id))){ 
        $sql = "DELETE FROM tblstudent WHERE id = " . $id;
    }
    else{
        die("Error: ID not Define");
    }

    $db = new DB();

    
    $connect = $db->connect();

    if (mysqli_query($connect, $sql)) {
        $response->getBody()->write("Record Delete Successfully");
    } 
    else {
        $response->getBody()->write("Error: Delete Record");
    }

    $db->closeConnection($connect);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
});

$app->run();
?>