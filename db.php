<?php
// Enter your Host, username, password, database below.
// I left password empty because i do not set password on localhost.
$conn = mysqli_connect("localhost", "rozhko40_iu4db", "oF&M0YU&", "rozhko40_iu4db");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?>
