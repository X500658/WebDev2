<?php
session_start();
// Check if the user is already logged in, if yes then redirect him to welcome page
if(!isset($_SESSION["loggedin"])){
header("location: index.php");
}

if(isset($_POST['logout'])){ 
session_destroy();
header("location: ../index.php");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mini_ismis";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

if($_SESSION['type'] ==="FACULTY"){
    $tables="schedules";
}else{
    $tables="classes";
}
$sql = "SELECT * FROM ".$tables." where ".$_SESSION['type']."_id=" .$_GET['id'];    
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}else{
    echo $sql;
}

function tabler(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql= "SELECT  schedule_session.schedule_id, users.first_name, users.last_name, subjects.name, schedule_session.day_of_week, schedule_session.start, schedule_session.end from schedules
    left join schedule_session
    on schedule_session.schedule_id=schedules.id 	
    left join classes
    on classes.schedule_id=schedules.id
    LEFT JOIN subjects
    on schedules.subject_id=subjects.id
    left JOIN users
    on users.id=schedules.faculty_id
    WHERE ".$_SESSION['type']."_id=" .$_SESSION['id']. 
    " ORDER BY `schedule_session`.`end` 
    ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            echo "<table class=\"table text-white\"><tr><th>Group #</th><th>Subject</th><th>Schedule</th><th>Time</th><th>Faculty</th></tr>";
					while($row = $result->fetch_assoc()) {
                        echo "<tr><td>" .$row['schedule_id']."</td><td>" .$row['name']. "</td><td>".$row['day_of_week']."</td><td>".$row['start']." - ".$row['end']."</td><td>".$row['first_name']." " .$row['last_name']."</td>";
                        echo "</tr>";
					}
					echo "</table>";
        }
    }
}

function stu_tabler(){      
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sqlOuter = "SELECT a.schedule_id,COUNT(*) FROM mini_ismis.schedule_session AS a
    INNER JOIN mini_ismis.schedules AS b 
    ON a.schedule_id=b.id
    INNER JOIN mini_ismis.classes AS c 
    ON c.schedule_id=b.id AND c.".$_SESSION['type']."_id=".$_SESSION['id']."
    GROUP BY schedule_id";
    $resultOuter = $conn->query($sqlOuter);
    if ($resultOuter->num_rows > 0){
        echo "<table class=\"table text-white\"><tr><th>Group #</th><th>Subject</th><th>Faculty</th><th>Day</th><th>Time</th>";
        while ($rowOuter=$resultOuter->fetch_assoc()) {
            $sql = "SELECT subjects.name, schedule_session.schedule_id, users.id as Uid, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end 
                    FROM `schedule_session`
                        inner join `schedules`
                        on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                        inner join subjects
                        on subject_id=subjects.id
                         inner join users
                         on schedules.faculty_id=users.id";
            $result = $conn->query($sql);
            if ($row = $result->fetch_assoc()){
                echo "<tr>"."<td rowspan={$rowOuter['COUNT(*)']}>".$row["schedule_id"]. "<td rowspan={$rowOuter['COUNT(*)']}>".$row["name"]."</td><td rowspan={$rowOuter['COUNT(*)']}>".$row["first_name"]." " .$row["last_name"]."</td>";
                echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
            }
            while($row = $result->fetch_assoc()){
                echo "<tr><td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                // echo "<td><a href=\"update.php?action=edit&id=".$row["id"]."\"><button class=\"btn btn-info btn-xs\" data-title=\"Edit\" data-toggle=\"modal\" data-target=\"#edit\" ><span class=\"glyphicon glyphicon-pencil\"></span></button></p></a></td>";
                // echo "<td><a href=\"view.php?action=delete&id=".$row["id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";

                // echo "<td><a href=\"update.php?action=edit&id=".$row["id"]."\"><button class=\"btn btn-info btn-xs\" data-title=\"Edit\" data-toggle=\"modal\" data-target=\"#edit\" ><span class=\"glyphicon glyphicon-pencil\"></span></button></a></td>";
                // echo "<tr><td>oten</td></tr>";
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "0 results<br>";
    }  
}

function fac_tabler(){      
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sqlOuter = "SELECT a.schedule_id,COUNT(*) FROM mini_ismis.schedule_session AS a
    INNER JOIN mini_ismis.schedules AS b 
    ON a.schedule_id=b.id
    GROUP BY schedule_id";
    $resultOuter = $conn->query($sqlOuter);
    if ($resultOuter->num_rows > 0){
        echo "<table class=\"table text-white\"><tr><th>Group #</th><th>Subject</th><th>Population</th><th>Day</th><th>Time</th>";
        while ($rowOuter=$resultOuter->fetch_assoc()) {
            $sql = "SELECT subjects.maximum_population, subjects.name, schedule_session.schedule_id, users.id as Uid, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end 
                    FROM `schedule_session`
                        inner join `schedules`
                        on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                        inner join subjects
                        on subject_id=subjects.id
                         inner join users
                         on schedules.faculty_id=users.id
                         where users.id=".$_SESSION['id'];
            $result = $conn->query($sql);
            if ($row = $result->fetch_assoc()){
                $sql2 = "SELECT * FROM classes
                where schedule_id=".$row['schedule_id'];
                $result2 = $conn->query($sql2);
                echo "<tr>"."<td rowspan={$rowOuter['COUNT(*)']}>".$row["schedule_id"]. "<td rowspan={$rowOuter['COUNT(*)']}>".$row["name"]."</td>";
                echo "<td rowspan={$rowOuter['COUNT(*)']} ";
                if($result2->num_rows==$row["maximum_population"])
                    echo "class=\"text-danger font-weight-bold\"";
                else if($result2->num_rows>=floor($row["maximum_population"]*0.70))
                    echo "class=\"text-warning\"";
            echo">".$result2->num_rows. "/".$row["maximum_population"]."</td>";
                echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
            }
            while($row = $result->fetch_assoc()){
                echo "<tr><td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "0 results<br>";
    }  
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>minIsmis - Profile</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body class="container bg-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-success circ spacer">
        <a class="navbar-brand title" href="../index.php">min<span class="white">Ismis</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php if($_SESSION['type']==="ADMIN"):?>
                    <li class="nav-item">
                        <a class="nav-link" href="../schedule.php"><span>Schedule</span></a>
                    </li>
                <?php endif?>
                <li class="nav-item">
                    <a class="nav-link" href="../subjects.php"><span>Subjects</span></a>
                </li>
                <li class="nav-item dropdown">
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item dropdown acct">
                    <a class="nav-link dropdown-toggle black " href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="black title">Welcome <span
                                class="white"><?php echo $_SESSION['name']?></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="./user/<?php echo $_SESSION['name']?>.php">View
                            Account</a>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                            <button type="submit" class="dropdown-item" name="logout">Log Out</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <div class="row justify-content-around bg-success text-white circ spacer">
        <div class="col-4  sidebar">
            <h2 class="text-center">Profile</h2>
            <ul class="list-group">
                    <li class="info">Name: <?php echo $_SESSION['name'];?></li>
                    <li class="info">Email: <?php echo $_SESSION['email'];?></li>
                    <li class="info">User Type: <?php echo $_SESSION['type']?></li>
                    <br>
                    <li class="info">Classes: <?php echo $result->num_rows?></li>
             </ul>
        </div>
        <div class="col-8">
            <h2 class="text-center">Classes</h2>
            <?php ($_SESSION['type']==="STUDENT")?stu_tabler():fac_tabler();?>
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