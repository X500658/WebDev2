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

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "DELETE FROM subjects WHERE id=" .$_GET['id'];
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        header("Location: subjects.php");
    }
}

if(isset($_POST['submit'])){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // $sql = "INSERT INTO subjects(name, maximum_population) VALUES('".$_POST['name']."','".$_POST['maxpop']."')";
    // if ($conn->query($sql) === TRUE) {
        // echo "New record created successfully";
    // } else {
    //     echo "Error: " . $sql . "<br>" . $conn->error;
    // }
}

function admin_tabler(){      
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
    // $sql = "SELECT subjects.id as subj, schedules.id, users.first_name,users.last_name, subjects.name from schedules
    // left join users
    // on faculty_id=users.id
    // left join subjects
    // on subject_id=subjects.id";
    $sql = "select * from subjects";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table class=\"table text-white\"><tr><th>Subject</th><th>Maximum Population</th><th>Edit</th><th>Delete</th></tr>";
            while ($row = $result->fetch_assoc()){
                echo "<tr><td>".$row["name"]."</td>";
                echo "<td>".$row["maximum_population"]."</td>";
                echo "<td><a href=\"update.php?info=subjects&id=".$row["id"]."\"><button class=\"btn btn-info btn-xs\" data-title=\"Edit\" data-toggle=\"modal\" data-target=\"#edit\" ><span class=\"glyphicon glyphicon-pencil\"></span></button></p></a></td>";
                echo "<td><a href=\"subjects.php?action=delete&id=".$row["id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                echo '</tr>';
            }
        echo "</table>";
    } else {
        echo "0 results<br>";
    } 
}

function stud_tabler(){      
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
    $sql = "select DISTINCT subjects.id, subjects.name, subjects.maximum_population from classes
    left join schedules
    on classes.schedule_id=schedules.id 
    left JOIN subjects
    on subjects.id=schedules.subject_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table class=\"table text-white\"><tr><th>Subject</th><th>Population</th></tr>";
            while ($row = $result->fetch_assoc()){
                echo "<tr><td>".$row["name"]."</td>";
                $sql2 = "SELECT * FROM classes
                left JOIN schedules as sch
                on sch.id=classes.schedule_id
                LEFT JOIN subjects as sb
                on sb.id=sch.subject_id
                left JOIN users
                on student_id=users.id
                where sb.id=".$row['id'];
                $result2 = $conn->query($sql2);
                echo "<td ";
                if($result2->num_rows==$row["maximum_population"])
                    echo "class=\"text-danger font-weight-bold\"";
                else if($result2->num_rows>=floor($row["maximum_population"]*0.70))
                    echo "class=\"text-warning\"";
                echo">".$result2->num_rows. "/".$row["maximum_population"]."</td>";
                echo '</tr>';
            }
        echo "</table>";
    } else {
        echo "0 results<br>$sql<br>";
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
    $sql = "select DISTINCT schedule_id, subjects.name, schedules.faculty_id, users.first_name, users.last_name from classes
    left join schedules
    on classes.schedule_id=schedules.id 
    left JOIN subjects
    on subjects.id=schedules.subject_id
    left join users
    on schedules.faculty_id=users.id
    where student_id=".$_SESSION['id'];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table class=\"table text-white\"><tr><th>Subject</th><th>Faculty</th></tr>";
            while ($row = $result->fetch_assoc()){
                echo "<tr><td>".$row["name"]."</td>";
                echo "<td>".$row["first_name"]. " ".$row['last_name']."</td>";
                echo '</tr>';
            }
        echo "</table>";
    } else {
        echo "0 results<br>$sql<br>";
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
    $sql = "SELECT distinct subjects.id, subjects.maximum_population, subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.start, schedule_session.end 
    FROM `schedule_session`
        inner join `schedules`
        on schedule_id=schedules.id
        inner join subjects
        on subject_id=subjects.id
        inner join users
        on schedules.faculty_id=users.id
        where users.id=".$_SESSION['id'].
        " order by schedule_session.schedule_id ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table class=\"table text-white\"><tr><th>Subject</th><th>Group #</th><th>Population</th><th>Day</th><th>Time</th><th>Delete</th></tr>";
        while($row = $result->fetch_assoc()){
            $sql2 = "SELECT * FROM classes
                where schedule_id=".$row['schedule_id'];
                $result2 = $conn->query($sql2);
            echo "<tr><td><a class=\"text-reset\" href=\"classes.php?id=".$row["schedule_id"]."&name=".$row['name']."\"><u>".$row["name"]."</u></a></td><td>".$row['schedule_id']."</td>";
            echo "<td ";
                if($result2->num_rows==$row["maximum_population"])
                    echo "class=\"text-danger font-weight-bold\"";
                else if($result2->num_rows>=floor($row["maximum_population"]*0.70))
                    echo "class=\"text-warning\"";
            echo">".$result2->num_rows. "/".$row["maximum_population"]."</td>";
            // echo "<td>".$row["day_of_week"]."</td>";
            $sql2="select day_of_week from schedule_session where schedule_id=" .$row['schedule_id'];
            $result2 = $conn->query($sql2);
            if ($result2->num_rows > 0) {
                echo "<td>";
                while($row2 = $result2->fetch_assoc()){
                    foreach( $row2 as $element) {
                        echo $element. " ";
                    }
                }
                echo "</td>";
            }
            echo "<td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td></a>";
            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
            echo "</tr>";
        }
    }else{
        echo "0 results";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>minIsmis - View Subjects</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="style.css" />
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<body class="container bg-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-success circ spacer">
        <a class="navbar-brand title" href="index.php">min<span class="white">Ismis</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
            <?php if($_SESSION['type']==="ADMIN"):?>
                    <li class="nav-item">
                        <a class="nav-link" href="/schedule.php"><span>Schedule</span></a>
                    </li>
            <?php endif?>
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
        <div class="row justify-content-around spacer">
            <?php if ($_SESSION['type']==="ADMIN"): ?>
                <div class="col-3 text-white">
                    <h2>Add Subject</h2>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
                    <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <label for="maxpop">Maximum Population</label>
                            <input type="text" class="form-control" id="maxpop" name="maxpop" placeholder="Maximum Population" required>
                        </div>
                        <button type="submit" class="btn l-green" name="submit">Add Subject</button>
                    </form>
                </div>
            <?php elseif ($_SESSION['type']==="STUDENT"): ?>
                <div class="col-3 text-white">
                    <h2>Enroll Subject</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                            <div class="form-group">
                                <label for="subj">Select Subject:</label>
                                <select class="form-control" id="subj" name="subj">
                                    <option selected>Choose...</option>
                                    <?php
                                    $servername = "localhost";
                                    $username = "root";
                                    $password = "";
                                    $dbname = "mini_ismis";
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $sql = "SELECT * FROM subjects order by id";
                                    $sql2="select DISTINCT subjects.id from classes
                                    left join schedules
                                    on classes.schedule_id=schedules.id 
                                    left JOIN subjects
                                    on subjects.id=schedules.subject_id
                                            where student_id=" .$_SESSION['id']. 
                                            " order by id";
                                    $result = $conn->query($sql);
                                    $result2 = $conn->query($sql2);
                                    if ($result->num_rows > 0) {
                                        
                                        if ($result2->num_rows > 0) {
                                            $lim=$result2->num_rows;
                                            $row2 = $result2->fetch_assoc();
                                            echo "<option>row2 ";
                                                        print_r($row2);
                                                        echo "</option>";
                                            while($row = $result->fetch_assoc()) {
                                                $sql3 = "SELECT * FROM classes
                                                    left JOIN schedules as sch
                                                    on sch.id=classes.schedule_id
                                                    LEFT JOIN subjects as sb
                                                    on sb.id=sch.subject_id
                                                    left JOIN users
                                                    on student_id=users.id
                                                    where sb.id=".$row['id'];
                                                $result3 = $conn->query($sql3);
                                                if($row['id']!=$row2['id'] && $result3->num_rows<$row['maximum_population']){
                                                    echo "<option value=" .$row["id"].">".$row["name"]."</option>";
                                                    if($result3->num_rows<$row['maximum_population'] && $lim>0){
                                                        $lim--;
                                                        $row2 = $result2->fetch_assoc();
                                                        echo "<option>not max";
                                                        print_r($row2);
                                                        echo "</option>";
                                                    }
                                                }else{
                                                    if($$lim>0){
                                                        $lim--;
                                                        $row2 = $result2->fetch_assoc();
                                                        echo "<option>!=";
                                                        print_r($row2);
                                                        echo "</option>";
                                                    }
                                                }
                                            }
                                        }else{
                                            echo $result2;
                                            while($row = $result->fetch_assoc()) {
                                                echo "<option value=" .$row["id"].">".$row["id"]."-" .$row["name"]."</option>"; 	
                                            }
                                        }
                                    } else {
                                        echo "0 results";
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn l-green" name="submit">Enroll</button>
                            <br><br>
                        </form>
                </div>
            <?php endif?>
            <?php if ($_SESSION['type']!="FACULTY"): ?>
            <div class="col-<?php if($_SESSION['type']==="ADMIN"){
                                    echo"9";
                                }else if($_SESSION['type']==="STUDENT"){
                                    echo "3";}?>text-white">
                <h2 class="text-center text-white">Available Subjects</h2>
                <?php
                    if($_SESSION['type']=="ADMIN"){
                        admin_tabler();
                    }else{
                        stud_tabler();
                    }
                ?>
            </div>
            <?php endif?>
            <?php if ($_SESSION['type']!="ADMIN"): ?>
                <div class="col-<?php echo($_SESSION['type']==="FACULTY")?"12":"3"?> text-white">
                    <h2 class="text-center">My Subjects</h2>
                    <?php if($_SESSION['type']==="FACULTY"){
                            fac_tabler();
                        }else if($_SESSION['type']==="STUDENT"){
                            stu_tabler();
                        }
                    ?>
                </div>
            <?php endif?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
</body>

</html>