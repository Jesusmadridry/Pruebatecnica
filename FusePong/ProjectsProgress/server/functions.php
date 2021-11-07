<?php 
require_once 'conexion.php';

$db = 'ieluiscarloslope_fusepong';

// Functions
function verifyUserExist(){
    global $conn, $db;
    $data=$_POST['data'];
    $response=false;
    $username=$data['username'];
    if($username !== ''){
        $sql="SELECT username FROM  $db.users WHERE username='{$username}'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row['username'] == $username){
                $response =  true;
            } 
        } 
    } 
    $conn->close();
    return $response;
}

function verifyLogin(){
    global $conn, $db;
    $data=$_POST['data'];
    $username=$data['username'];
    $password=$data['password'];
    $password=addslashes($password);
    //$password=password_hash($password, PASSWORD_DEFAULT);
    if($username !=='' &&  $password !== ''){
        $sql="SELECT username, password FROM  $db.users WHERE username='{$username}'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row['username'] == $username && password_verify($password, $row['password'])){
                   echo json_encode(array('result' => true, 'response' => 'Login Successful'));
                } else {
                     echo json_encode(array('result' => false, 'response' => 'Username and password not match '));
                    
                }
            }
        } else {
             echo json_encode(array('result' => false, 'response' => 'Username not finded'));
            return false;
        }
    } else {
          echo json_encode(array('result' => false, 'response' => 'Username or password is empty'));
    }
    $conn->close();
}

function verifyProjectExist(){
    global $conn, $db;
    $data=$_POST['data'];
    $name=$data['name'];
    $cid=$data['cid'];
    $response=false;
    if($name !=='' &&  $cid !== ''){
        $sql="SELECT name, cid FROM  $db.projects WHERE name='{$name}' AND cid='{$cid}' ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row['name'] == $name && $row['cid'] == $cid){
                $response = true;
            } 
        }
    } 
    return $response;
}


function getCompanies(){
    if (isset($_POST["data"])){
        global $conn, $db;
        $companies = array();
        $data=$_POST['data'];
        $condit=empty($data['id']) ? '' : "WHERE id={$data['id']}";
        $sql="SELECT * FROM  $db.companies  $condit";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $company = array('id'          =>   $row['id'],
                                'cpn_name'     =>   $row['cpn_name'],  
                                'cpn_nit'      =>   $row['cpn_nit'],   
                                'cpn_address'  =>   $row['cpn_address'],   
                                'cpn_tel'      =>   $row['cpn_tel'],   
                                'cpn_email'    =>   $row['cpn_email'],   
                );
                array_push($companies, $company);        
            }
            echo json_encode(array('result' => true, 'list' => $companies));
        } else {
            echo json_encode(array('result' => false, 'list' => $companies));
        }
    }
    $conn->close();
}

function createProject() {
    global $conn, $db;
    $data=$_POST['data'];
    $name=$data['name'];
    $cid=$data['cid'];
    $developer=$data['username'];
    
    $name=addslashes($name);

    if(!verifyProjectExist()){
        $sql="INSERT INTO $db.projects (name, cid, developer) VALUES ('{$name}', '{$cid}', '{$developer}')";
        if ($conn->query($sql) == true) {
             echo json_encode(array('result' => true, 'response' => 'Project created successfuly'));
        } else {
             echo json_encode(array('result' => false, 'response' => $sql));
        }
    } else {
         echo json_encode(array('result' => false, 'response' => 'Project exists'));
    }
    $conn->close();
}

function getProjects(){
    if (isset($_POST["data"])){
        global $conn, $db;
        $projects = array();
        $data=$_POST['data'];
        $cid=$data['cid'];
        $username=$data['username'];
        $sql="SELECT * FROM  $db.projects WHERE cid=$cid AND developer='$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $project = array('id'   =>    $row['id'],
                                'name'  =>    $row['name'],  
                                'cid'   =>    $row['cid'],
                );
                if(!empty($project['cid'])){
                    $sql="SELECT cpn_name FROM  $db.companies WHERE id='{$project['cid']}'";
                    $result2 = $conn->query($sql);
                    if ($result2->num_rows > 0) {
                        $row2 = $result2->fetch_assoc();
                        $project['cpn'] = $row2['cpn_name'];
                    }
                }
                array_push($projects, $project);        
            }
            // echo "<pre>";
            // print_r($companies);
            echo json_encode(array('result' => true, 'list' => $projects));
        } else {
            echo json_encode(array('result' => false, 'list' => $projects));
        }
    }
    $conn->close();
}


function createUser() {
    global $conn, $db;
    $data=$_POST['data'];
    $username=$data['username'];
    $password=$data['password'];
    $company_id=$data['company_id'];
    $password=addslashes($password);
    $password=password_hash($password, PASSWORD_DEFAULT);
    if(!verifyUserExist()){
        $sql="INSERT INTO $db.users (username, password, cpn_id) VALUES ('{$username}', '{$password}', '{$company_id}')";
        if ($conn->query($sql) == true) {
             echo json_encode(array('result' => true, 'response' => 'User created successfuly'));
        } else {
             echo json_encode(array('result' => false, 'response' => $sql));
        }
    } else {
         echo json_encode(array('result' => false, 'response' => 'Username exists'));
    }
    $conn->close();
}


function getUser(){
    if (isset($_POST["data"])){
        global $conn, $db;
        $user = array();
        $data=$_POST['data'];
        $username=$data['username'];
        $sql="SELECT * FROM  $db.users WHERE username='$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user['username']   =  $row['username'];
            $user['cpn_id']     =  $row['cpn_id'];
            echo json_encode(array('result' => true, 'user' => $user));
        } else {
            echo json_encode(array('result' => false, 'user' => $user));
        }
    }
    $conn->close();
}

function createTicket() {
    global $conn, $db;
    $data=$_POST['data'];
    $comments=$data['comments'];
    $pid=$data['pid'];

    $comments=addslashes($comments);
    $sql="INSERT INTO $db.functionalities (comments, status, pid) VALUES ('{$comments}', 'Active', '{$pid}')";
    if ($conn->query($sql) == true) {
        echo json_encode(array('result' => true, 'response' => 'Ticket created successfuly'));
    } else {
        echo json_encode(array('result' => false, 'response' => $sql));
    }
    $conn->close();
}

function getTickets(){
    if (isset($_POST["data"])){
        global $conn, $db;
        $tickets = array();
        $data=$_POST['data'];
        $pid=$data['pid'];
        $sql="SELECT f.id, f.comments, f.status, f.pid, p.name, p.developer FROM  $db.functionalities  f INNER JOIN $db.projects p ON pid = p.id  WHERE pid=$pid";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $ticket = array('id'        =>    $row['id'],
                                'comments'  =>    $row['comments'],  
                                'status'    =>    $row['status'], 
                                'pid'       =>    $row['pid'],
                                'pname'     =>    $row['name'],
                                'developer' =>    $row['developer'],
                                'sql' => $sql
                );
                array_push($tickets, $ticket);        
            }
            echo json_encode(array('result' => true, 'list' => $tickets));
        } else {
            echo json_encode(array('result' => false, 'list' => $tickets));
        }
    }
    $conn->close();
}

function getTicket(){
    if (isset($_POST["data"])){
        global $conn, $db;
        $data=$_POST['data'];
        $tid=$data['tid'];
        $ticket = array();
        $sql="SELECT * FROM  $db.functionalities WHERE id='$tid'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $ticket['id']       =  $row['id'];
            $ticket['comments'] =  $row['comments'];
            $ticket['status']   =  $row['status'];
            $ticket['pid']   =  $row['pid'];
            echo json_encode(array('result' => true, 'ticket' => $ticket));
        } else {
            echo json_encode(array('result' => false, 'ticket' => 'Ticket not finded'));
        }
    }
    $conn->close();
}

function editTicket(){
    if (isset($_POST["data"])){
        global $conn, $db;
        $data=$_POST['data'];
        $tid=$data['tid'];
        $comments=$data['comments'];
        $status=$data['status'];

        $comments=addslashes($comments);
        $sql="UPDATE $db.functionalities SET comments='{$comments}', status='{$status}' WHERE id='$tid'";
        if ($conn->query($sql) == true) {
            echo json_encode(array('result' => true, 'response' => 'Ticket updated successfuly'));
        } else {
                echo json_encode(array('result' => false, 'response' => $sql));
        }
    }
    $conn->close();
}   

function cancelTicket(){
    if (isset($_POST["data"])){
        global $conn, $db;
        $data=$_POST['data'];
        $tid=$data['tid'];
        $sql="DELETE FROM $db.functionalities WHERE id='$tid'";
        if ($conn->query($sql) == true) {
            echo json_encode(array('result' => true, 'response' => 'Ticket canceled successfuly'));
        } else {
                echo json_encode(array('result' => false, 'response' => $sql));
        }
    }
    $conn->close();
} 



// Getting Function name
if(isset($_POST['data'])){
    $opc=$_POST['data']['functionname'];
    switch($opc){
        case 'getCompanies'   :  getCompanies();             break;
        case 'getProjects'    :  getProjects();              break;
        case 'getUser'        :  getUser();                  break;
        case 'verifyLogin'    :  verifyLogin();              break;
        case 'createUser'     :  createUser();               break;
        case 'createProject'  :  createProject();            break;
        case 'createTicket'   :  createTicket();             break;
        case 'getTickets'     :  getTickets();               break;
        case 'getTicket'      :  getTicket();                break;
        case 'editTicket'     :  editTicket();               break;
        case 'cancelTicket'   :  cancelTicket();             break;
    }
}




?>