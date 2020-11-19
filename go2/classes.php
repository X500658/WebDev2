<?php
session_start();

    // Check if the user is already logged in, if yes then redirect him to welcome page
if(!isset($_SESSION["loggedin"])){
    header("location: index.php");
}

if(isset($_POST['logout'])){ 
    session_destroy();
    header("location: index.php");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>minIsmis - Classes</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body class="container bg-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-success circ spacer">
        <a class="navbar-brand title" href="index.php">min<span class="white">Ismis</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="subjects.php"><span>Subjects</span></a>
                </li>
                <?php if ($_SESSION['type']==="ADMIN") : ?>
                    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span>People</span>
        </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="users/faculty.php">Faculty</a>
                        <a class="dropdown-item" href="users/students.php">Students</a>
                    </div>
                </li>
                <?php endif ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item dropdown acct">
                    <a class="nav-link dropdown-toggle black " href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="black title">Welcome <span class="white"><?php echo $_SESSION['name']?></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <?php
                          if($_SESSION['type']!= "ADMIN"){
                            echo "<a class=\"dropdown-item\" href=\"users/profile.php?id=" .$_SESSION["id"]. "\">View Account</a>";
                            }
                        ?>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                         <button type="submit" class="dropdown-item" name="logout">Log Out</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <div class="bg-success circ spacer">
        <div class="row">
            <div class="col-4 text-white sidebar">
                <h2 class="text-center">Profile</h2>
                <ul class="list-group">
                <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "mini_ismis";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $sql = "SELECT * from subjects where name='".$_GET['name']."'";
                    $sql2= "select * from classes where schedule_id=".$_GET['id'];
                    $result = $conn->query($sql);
                    $result2 = $conn->query($sql2);
                    echo "<li class=\"info\">Subject: ".$_GET['name']."</li>";
                    echo "<li class=\"info\">Group Number: ". $_GET['id']."</li>
                    <li class=\"info\">Population: ";
                        if($result2->num_rows>0){
                            echo $result2->num_rows;
                        }else{
                            echo "0";
                        }
                    echo "/";
                    echo $result->fetch_assoc()['maximum_population'];
                    echo "</li>";
                ?>
                </ul>
                <br>
            </div>
            <div class="col-8">
            <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "mini_ismis";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $sql = "SELECT * FROM classes 
                    left join users
                    on classes.student_id=users.id
                    where schedule_id=".$_GET['id'];
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        echo "<h2 class=\"text-white text-center\">Students</h2>";
                        echo "<table class=\"table text-white\"><tr><th>Name</th><th>Email</th></tr>";
                        while($row = $result->fetch_assoc()){
                            echo "<tr>
                                <td>".$row['first_name']. " " .$row['last_name']. "</td>
                                <td>".$row['email']. "</td>
                                </tr>";
                        }
                    }else{
                        echo "<h3 class=\"text-white text-center\">No Student Enrolled";
                    }
                    $conn->close();
            ?>
            </div>
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