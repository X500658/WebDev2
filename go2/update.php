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

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mini_ismis";
if (isset($_GET['info'])){
    $info= $_GET['info'];
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM ".$info." where id=" .$_GET['id'];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $conn->close();
}

function boxer(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql="SELECT * FROM `subjects` order by id ASC";
    $result = $conn->query($sql);
    $sql2="SELECT users.id, subjects.id as subj, schedules.id, users.first_name,users.last_name, subjects.name from schedules
    left join users
    on faculty_id=users.id
    left join subjects
    on subject_id=subjects.id
    where users.id=".$_GET['id']. "
    order by users.id ASC";
    $result2 = $conn->query($sql2);
    if ($result->num_rows > 0 && $result2->num_rows >0){
        $row2 = $result2->fetch_assoc();
        while ($row = $result->fetch_assoc()){
            echo "<div class=\"form-check\">";
            echo "<input class=\"form-check-input\" type=\"checkbox\" value=\"".$row['id']."\" id=\"".$row['name']."\" name=\"classes[]\"";
            if($row['id']==$row2['subj']){
                echo "checked";
                $row2 = $result2->fetch_assoc();
            }
            echo ">";
            echo "<label class=\"form-check-label\" for=\"".$row['name']."\">";
            echo $row['name'];
            echo "</label>";
            echo "</div>";
        }
    }
    $conn->close();
}

if(isset($_POST['submit'])){
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if($info=='subjects'){
        $sql = "UPDATE ".$info." SET name = '".$_POST['name']."', maximum_population='".$_POST['maxpop']."' WHERE id =" .$_GET['id'];
        if ($conn->query($sql) === TRUE) {
            header("location: " .$info. ".php");
        }else{
            echo "Error updating record: " . $conn->error;
        }
        
    }else if($info=='users'){
        // $sql = "INSERT INTO schedules (subject_id, faculty_id) VALUES ('".$_POST['subj']."','".$_POST['facu']."')";
        //             if (!mysqli_query($conn, $sql)) {
        //             // } else {
        //                 echo "Error: Duplicate Entry";
        //             }
        foreach($_POST['classes'] as $selected) {
            $sql = "INSERT INTO schedules (subject_id, faculty_id) VALUES ('".$selected."','".$_GET['id']."')";
                    if (!mysqli_query($conn, $sql)) {
                    // } else {
                        echo "Error: Duplicate Entry";
                    }
            }
    }       
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>minIsmis - Edit</title>
</head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="style.css" />

<body class="container bg-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-success circ spacer">
        <a class="navbar-brand title" href="index.php">min<span class="white">Ismis</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php"><span>Home</span></a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="schedule.php"><span>Schedule</span></a>
                </li>
                <li class="nav-item <?php if($_GET['info']=='subject'){echo "active";}?>">
                    <a class="nav-link" href="subjects.php"><span>Subjects</span></a>
                </li>
                <?php if ($_SESSION['type']==="ADMIN") : ?>
                    <li class="nav-item dropdown <?php if($_GET['info']=='users'){echo "active";}?>">
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
    <div class="row justify-content-center">
        <div class="col-6 bg-success circ spacer text white">
		    <h2 class="text-center">Edit <?php echo ucwords($info);?></h2>
            <form method="post">
                <?php if($info=='subjects'){
                    echo"<div class=\"form-group\">
                            <label for=\"name\">Name</label>
                            <input type=\"text\" class=\"form-control\" id=\"name\" name=\"name\" value=".$row['name']." required>
                        </div>
                        <div class=\"form-group\">
                            <label for=\"maxpop\">Maximum Population</label>
                            <input type=\"text\" class=\"form-control\" id=\"maxpop\" name=\"maxpop\" value=".$row['maximum_population']." required>
                        </div>";
                }else if ($info=='users'){
                    echo "<h5>Classes of <span class=\"white\">".$row['first_name']. " " .$row['last_name']."</h5>";
                    boxer();
                    echo "</br>";
                }
                ?>
                <button type="submit" class="btn l-green" name="submit">Edit <?php echo ucwords($info);?></button>
                </br></br>
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