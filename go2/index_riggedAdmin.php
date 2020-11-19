<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION['name'])){
    if ($_SESSION['type']!="ADMIN"){
        header("location: ./users/profile.php?id=".$_SESSION['id']."");
      }else{
          header("location: schedule.php");
      }
  exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mini_ismis";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
if(isset($_POST['signup'])){
	$fname= $_POST['fname'];
	$lname= $_POST['lname'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $type= $_POST['type'];
	$sql = "INSERT INTO users (first_name, last_name, email, password, user_type)
          VALUES ('".$fname."','".$lname."','".$email."','".$pass."','".$type."')";
	if (!mysqli_query($conn, $sql)) {
	// } else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
}

if(isset($_POST['login'])){ 
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  if($email=="admin" && $pass=="admin"){
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = 0;
        $_SESSION["name"] = "Admin";
        $_SESSION['email']= $email;
        $_SESSION['type']="ADMIN";
            header("location: schedule.php");
  }else{
    $sql = "SELECT * from users where email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $row['id'];
            $_SESSION["name"] = $row['first_name']." " .$row['last_name'];
            $_SESSION['email']= $row['email'];
            $_SESSION['type']=$row['user_type'];
            if ($_SESSION['type']!="ADMIN"){
                header("location: ./users/profile.php?id=".$_SESSION['id']."");
            }else{
                header("location: schedule.php");
            }
        }
    $conn->close();
    }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>minIsmis - Log in</title>
</head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel = "stylesheet" type = "text/css" href = "style.css" />
<body class="container bg-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-success circ spacer">
        <a class="navbar-brand title" href="../index.php">min<span class="white">Ismis</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>
    <div class="row justify-content-around spacer">
        <div class="col-5 bg-success text-white circ">
            <h2 class="text-center">Sign Up</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" class="form-control" id="fname" name="fname" required>
                </div>
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" class="form-control" id="lname" name="lname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="pass">Password</label>
                    <input type="password" class="form-control" id="pass" name="pass" required>
                </div>
                <div class="form-group">
                    <label for="type">Select Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option selected disabled>Choose...</option>
                        <option value="faculty">Faculty</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <button type="submit" class="btn l-green" name="signup">Sign Up</button>
            </form>
        </div>
        <div class="col-5 bg-success text-white circ">
            <h2 class="text-center">Log In</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="pass">Password</label>
                    <input type="password" class="form-control" id="pass" name="pass" required>
                </div>
                <button type="submit" class="btn l-green" name="login">Log In</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
</body>

</html>