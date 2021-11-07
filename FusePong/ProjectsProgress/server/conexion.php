<?php

$servername = "ieluiscarloslopez.edu.co";
$username = "ieluiscarloslope_superuser";
$password = "fusepong1*";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

?>
