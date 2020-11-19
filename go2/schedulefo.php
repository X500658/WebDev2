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
    $sql = "DELETE FROM schedule_session WHERE id=" .$_GET['id'];
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        header("Location: schedule.php");
    }
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
    if (isset($_POST['subject']) && $_POST['subject']!='none' && $_POST['subject']!='remove'){
        $sqlOuter = "SELECT schedule_id,COUNT(*) FROM mini_ismis.schedule_session AS a
        INNER JOIN mini_ismis.schedules AS b ON a.schedule_id=b.id AND b.subject_id={$_POST['subject']}
        GROUP BY schedule_id";
    }else if (isset($_POST['faculty']) && $_POST['faculty']!='none' && $_POST['faculty']!='remove'){
        $sqlOuter = "SELECT schedule_id,COUNT(*) FROM mini_ismis.schedule_session AS a
        INNER JOIN mini_ismis.schedules AS b ON a.schedule_id=b.id AND b.faculty_id={$_POST['faculty']}
        GROUP BY schedule_id";
    }else {
        $sqlOuter = "SELECT schedule_id,COUNT(*) FROM mini_ismis.schedule_session GROUP BY schedule_id";
    }
    $resultOuter = $conn->query($sqlOuter);
    if (isset($_POST['subject']) && $_POST['subject']=="remove"){
        echo "<table class=\"table text-white\"><tr><th>Faculty</th><th>Day</th><th>Time</th><th>Delete</th></tr>";
    }else if (isset($_POST['faculty']) && $_POST['faculty']=="remove"){
        echo "<table class=\"table text-white\"><tr><th>Subject</th><th>Day</th><th>Time</th><th>Delete</th></tr>";
    }else {
        echo "<table class=\"table text-white\"><tr><th>Subject</th><th>Faculty</th><th>Day</th><th>Time</th><th>Delete</th></tr>";
    }
    if ($resultOuter->num_rows > 0){
        $shown = 0;
        while ($rowOuter=$resultOuter->fetch_assoc()) {
            if (isset($_POST['subject']) || isset($_POST['faculty'])){
                if (isset($_POST['subject'])){
                    if ($_POST['subject']=="remove"){
                        $sql = "SELECT schedule_session.schedule_id,users.first_name, users.last_name,schedule_session.day_of_week, schedule_session.start, schedule_session.end 
                        FROM `schedule_session`
                        inner join `schedules`
                        on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                        inner join users
                        on schedules.faculty_id=users.id";
                        $result = $conn->query($sql);
                        if ($row = $result->fetch_assoc()){
                            echo "<tr><td rowspan={$rowOuter['COUNT(*)']}>".$row["first_name"]." " .$row["last_name"]."</td>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }else if ($_POST['subject']!="none"){
                        $filter = $_POST['subject'];
                        $sql = "SELECT subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end 
                        FROM `schedule_session`
                            inner join `schedules`
                            on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                            inner join subjects 
                            on subject_id=subjects.id AND subjects.id={$filter}
                            inner join users
                            on schedules.faculty_id=users.id";
                        $result = $conn->query($sql);
                        if ($row = $result->fetch_assoc()){
                            echo "<tr><td rowspan={$rowOuter['COUNT(*)']}>".$row["name"]."</td><td rowspan={$rowOuter['COUNT(*)']}>".$row["first_name"]." " .$row["last_name"]."</td>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }
                }else {
                    if ($_POST['faculty']=="remove"){
                        $sql = "SELECT subjects.name, schedule_session.schedule_id, schedule_session.day_of_week, schedule_session.start, schedule_session.end 
                        FROM `schedule_session`
                            inner join `schedules`
                            on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                            inner join subjects
                            on subject_id=subjects.id";
                        $result = $conn->query($sql);
                        if ($row = $result->fetch_assoc()){
                            echo "<tr><td rowspan={$rowOuter['COUNT(*)']}>".$row["name"]."</td>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }else if ($_POST['faculty']!="none"){
                        $filter = $_POST['faculty'];
                        $sql = "SELECT subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end 
                        FROM `schedule_session`
                            inner join `schedules`
                            on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                            inner join subjects
                            on subject_id=subjects.id
                            inner join users
                            on schedules.faculty_id=users.id AND schedules.faculty_id={$filter}";
                        $result = $conn->query($sql);
                        if ($row = $result->fetch_assoc()){
                            echo "<tr><td rowspan={$rowOuter['COUNT(*)']}>".$row["name"]."</td><td rowspan={$rowOuter['COUNT(*)']}>".$row["first_name"]." " .$row["last_name"]."</td>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<tr>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }
                }
            }
            if ( $shown==0 ){
                $sql = "SELECT subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end 
                FROM `schedule_session`
                    inner join `schedules`
                    on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                    inner join subjects
                    on subject_id=subjects.id
                    inner join users
                    on schedules.faculty_id=users.id";
                $result = $conn->query($sql);
                if ($row = $result->fetch_assoc()){
                    echo "<tr><td rowspan={$rowOuter['COUNT(*)']}>".$row["name"]."</td><td rowspan={$rowOuter['COUNT(*)']}>".$row["first_name"]." " .$row["last_name"]."</td>";
                    echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                    echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                    echo "</tr>";
                }
                while($row = $result->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                    echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                    echo "</tr>";
                }
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

function tabler(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT distinct subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.start, schedule_session.end 
    FROM `schedule_session`
        inner join `schedules`
        on schedule_id=schedules.id
        inner join subjects
        on subject_id=subjects.id
        inner join users
        on schedules.faculty_id=users.id
        order by schedule_session.schedule_id ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table class=\"table text-white\"><tr><th>Subject</th><th>Faculty</th>";
        echo "<th>Day</th>";
        echo "<th>Time</th><th>Delete</th></tr>";
        while($row = $result->fetch_assoc()){
            echo "<tr><td>".$row["name"]."</td><td>".$row["first_name"]." " .$row["last_name"]."</td>";
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
            echo "<td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
            echo "</tr>";
        }
    }else{
        echo "0 results";
    }
}

function timer($time){
    $i=7;
    $lim=$i+11;
    if($time==2){
        $i++;
        $lim++;
    }
    for(;$i<=$lim;$i++){
        for($j=0; $j<2;$j++){
            echo "<option>";
            if($i<12){
                echo $i;
            }else if($i==12){
                echo $i;
            }else{
                echo $i-12;
            }
            echo ":";
            echo ($j==0)?"00":$j*30;
            echo " ";
            if($i<12){
                echo"AM";
            }else if($i==12 && $j==0){
                echo "NN";
            }else{
                echo "PM";
            }
            echo "</option>";
        }
    }
    if($time==1){
        echo "<option>7:00 PM</option>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>minIsmis - View Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>    
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
                    <a class="nav-link" href="schedule.php"><span>Schedule</span></a>
                </li>
                <li class="nav-item">
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button"
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
            <?php if ($_SESSION['type']=="ADMIN"):?>
                <div class="col-3 text-white">
                    <div class="justify-content-around bor circ spacer">
                        <h2 class="text-center">Add Schedule</h2>
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
                                    $sql = "SELECT * FROM subjects";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<option value=" .$row["id"].">" .$row["name"]."</option>"; 	
                                        }
                                    } else {
                                        echo "0 results";
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="facu">Select Faculty:</label>
                                <select class="form-control" id="facu" name="facu">
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
                                    $sql = "SELECT * FROM `users` WHERE user_type='FACULTY'";
                                    $result = $conn->query($sql);
                                    print_r($result);
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<option value=" .$row["id"].">" .$row["first_name"]. " ".$row['last_name']."</option>"; 	
                                        }
                                    } else {
                                        echo "0 results";
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Days:</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="mon" value="MON" name="days[]">
                                        <label class="form-check-label" for="mon">Monday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="tue" value="TUE"  name="days[]">
                                        <label class="form-check-label" for="tue">Tuesday</label>
                                    </div><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="wed" value="WED" name="days[]">
                                        <label class="form-check-label" for="wed">Wednesday</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="thu" value="THU"  name="days[]">
                                            <label class="form-check-label" for="thue">Thursday</label>
                                    </div><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="fri" value="FRI" name="days[]">
                                        <label class="form-check-label" for="fri">Friday</label>
                                    </div>
                            </div>
                            <div class="form-group">
                                <label for="stime">Select Start Time:</label>
                                <select class="form-control" id="stime" name="stime">
                                    <option selected disabled>Choose...</option>
                                    <?php timer(1)?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="etime">Select End Time:</label>
                                <select class="form-control" id="etime" name="etime">
                                    <option selected disabled>Choose...</option>
                                    <?php timer(2)?>
                                </select>
                            </div>
                            <button type="submit" class="btn l-green" name="submit">Add Schedule</button>
                            <br><br>
                        </form>
                    </div>
                    <div class="row bor circ spacer justify-content-around">
                    <h2 class="text-center">Filter</h2>
                        <div class="form-check">
                        <?php if (!isset($_POST['faculty'])) : ?>
                            <form id="filterOption" action="./schedule.php" method="POST">
                                <input type="radio" id="selectSubjects" name="filter" checked="checked">
                                <label for="selectSubjects">Subjects</label>
                                <input type="radio" id="selectFaculties" name="filter">
                                <label for="selectFaculties">Faculty</label>
                                <input type="radio" id="reset" name="filter">
                                <label for="reset">Reset</label>
                            </form>
                        <?php else: ?>
                            <form id="filterOption" action="./schedule.php" method="POST">
                                <input type="radio" id="selectSubjects" name="filter">
                                <label for="selectSubjects">Subjects</label>
                                <input type="radio" id="selectFaculties" name="filter" checked="checked">
                                <label for="selectFaculties">Faculty</label>
                                <input type="radio" id="reset" name="filter">
                                <label for="reset">Reset</label>
                            </form>
                        <?php endif ?>
                        <form id="formSubject" action="./schedule.php" method="POST">
                            <label for="subject">Select Subject:</label>
                            <select class="filterSubject" name="subject">
                                <option value="none" selected="selected">None</option>
                                <option value="remove">Remove</option>
                                <?php 
                                    $conn = new mysqli('localhost','root','');
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $sql = "SELECT * FROM mini_ismis.subjects";
                                    $result = $conn->query($sql);
                                    while ($row=$result->fetch_assoc()) : ?>
                                        <option value="<?php echo $row['id'] ?>"
                                        <?php if (isset($_POST['subject']) && $_POST['subject']==$row['id']) : ?>
                                            selected="selected"
                                        <?php endif ?>
                                        > <?php echo $row['name'] ?></option>
                                <?php endwhile ;
                                    $conn->close();
                                ?>
                            </select>
                        </form>
                        <form id="formFaculty" action="./schedule.php" method="POST">
                            <label for="faculty">Select Faculty:</label>
                            <select class="filterFaculty" name="faculty">
                                <option value="none" selected="selected">None</option>
                                <option value="remove">Remove</option>
                                <?php 
                                    $conn = new mysqli('localhost','root','');
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $sql = "SELECT * FROM mini_ismis.users WHERE user_type='FACULTY'";
                                    $result = $conn->query($sql);
                                    while ($row=$result->fetch_assoc()) : ?>
                                        <option value="<?php echo $row['id'] ?>"
                                        <?php if (isset($_POST['faculty']) && $_POST['faculty']==$row['id']) : ?>
                                            selected="selected"
                                        <?php endif ?>
                                        > <?php echo $row['first_name'] ?> <?php echo $row['last_name'] ?></option>
                            <?php endwhile ;
                                    $conn->close();
                                ?>
                            </select>
                        </form>
                        </div>
                    </div>
                </div>
            <?php endif?>
            <div class="col-<?php echo ($_SESSION['type']=="ADMIN")?"8":"12";?>bg-light text-white circ">
                <h2 class="text-center text-white">Schedule</h2>
                <?php
                    if($_SESSION['type']=="ADMIN"){
                        admin_tabler();
                        // tabler();
                    }else if($_SESSION['type']==="FACULTY"){
                        fac_tabler();
                    }else{
                        tabler();
                    }
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
     <script>
        $(document).ready(function(){
            if ($("input[type=radio]:first-child").is(":checked")){
                $("#formSubject").css("display","block");
                $("#formFaculty").css("display","none");
            }else {
                $("#formSubject").css("display","none");
                $("#formFaculty").css("display","block");
            }
            $("input[type=radio]").on("click",function(){
                if ($("input[type=radio]:first-child").is(":checked")){
                    $("#formSubject").css("display","block");
                    $("#formFaculty").css("display","none");
                }else {
                    $("#formSubject").css("display","none");
                    $("#formFaculty").css("display","block");
                }
            });
            $("#reset").on("click",function(){
                $("#filterOption").submit();
            });
            $(".filterSubject").on("change",function(){
                $("#formSubject").submit();
            });
            $(".filterFaculty").on("change",function(){
                $("#formFaculty").submit();
            });
            $("#studentEnrollButton").on("click",function(){
                var subject = $("#studentEnrollSelect").find(":selected").val();
                $("#inputSubject").val(subject);
                $("#studentEnrollSubject").submit();
            });
            $(".valid").on("click",function(){
                $("input[name=schedule]").val($(this).next().attr('placeholder'));
                $("#insertSubjectStudent").submit();
            });
        });
    </script>
</body>

</html>