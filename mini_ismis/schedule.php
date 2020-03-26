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
                        $sql = "SELECT schedule_session.schedule_id,users.first_name, users.last_name,schedule_session.day_of_week, schedule_session.start, schedule_session.end,schedule_session.id AS schedule_session_id
                        FROM `schedule_session`
                        inner join `schedules`
                        on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                        inner join users
                        on schedules.faculty_id=users.id";
                        $result = $conn->query($sql);
                        if ($row = $result->fetch_assoc()){
                            echo "<tr><td rowspan={$rowOuter['COUNT(*)']}>".$row["first_name"]." " .$row["last_name"]."</td>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td></tr>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }else if ($_POST['subject']!="none"){
                        $filter = $_POST['subject'];
                        $sql = "SELECT subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end ,schedule_session.id AS schedule_session_id
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
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }
                }else {
                    if ($_POST['faculty']=="remove"){
                        $sql = "SELECT subjects.name, schedule_session.schedule_id, schedule_session.day_of_week, schedule_session.start, schedule_session.end ,schedule_session.id AS schedule_session_id
                        FROM `schedule_session`
                            inner join `schedules`
                            on schedule_id=schedules.id AND schedule_id={$rowOuter['schedule_id']}
                            inner join subjects
                            on subject_id=subjects.id";
                        $result = $conn->query($sql);
                        if ($row = $result->fetch_assoc()){
                            echo "<tr><td rowspan={$rowOuter['COUNT(*)']}>".$row["name"]."</td>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }else if ($_POST['faculty']!="none"){
                        $filter = $_POST['faculty'];
                        $sql = "SELECT subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end ,schedule_session.id AS schedule_session_id
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
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        while($row = $result->fetch_assoc()){
                            echo "<tr>";
                            echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                            echo "</tr>";
                        }
                        $shown = 1;
                    }
                }
            }
            if ( $shown==0 ){
                $sql = "SELECT subjects.name, schedule_session.schedule_id, users.first_name, users.last_name, schedule_session.day_of_week, schedule_session.start, schedule_session.end ,schedule_session.id AS schedule_session_id
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
                    echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                    echo "</tr>";
                }
                while($row = $result->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".$row["day_of_week"]."</td><td>".date('g:ia', strtotime($row["start"]))." - ".date('g:ia', strtotime($row["end"]))."</td>";
                    echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
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
            echo "<td><a href=\"schedule.php?action=delete&id=".$row["schedule_session_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
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


function TimeConstraint($possibleTime,$startEnd,$hourMinute,$numberInput,$meridian){
    if ($possibleTime==12 && $meridian=="am"){
        return false;
    }
    if ($possibleTime==8 && $meridian=="pm"){
        return false;
    }
    if (!isset($_POST['day_of_week']) || !isset($_POST['day_of_week'][$numberInput])){
        return true;
    }
    $startHour = $_POST['startHour'][$numberInput];
    $startMinute = $_POST['startMinute'][$numberInput];
    $endHour = $_POST['endHour'][$numberInput];
    $endMinute = $_POST['endMinute'][$numberInput];
    if ($startEnd=='start'){
        if ($hourMinute=="hour"){
            if ($startMinute=="00" && $endMinute=="30"){
                return AdjustMeridian($possibleTime) <= AdjustMeridian2($endHour,$meridian);
            }else {
                return AdjustMeridian($possibleTime) < AdjustMeridian2($endHour,$meridian);
            }
        }else { //$hourMinute=="minute"
            if ($endMinute == "30" && AdjustMeridian($startHour) == AdjustMeridian2($endHour,$meridian)){
                return $possibleTime=="00";
            }else if (AdjustMeridian($startHour)==0){ // No 7:00 AM class
                return $possibleTime=="30";
            }else {
                return true;
            }
        }
    }else { //$startEnd=="end"
        if ($hourMinute=="hour"){
            if ($startMinute=="00" && $endMinute=="30"){
                return AdjustMeridian2($possibleTime,$meridian) >= AdjustMeridian($startHour);
            }else {
                return AdjustMeridian2($possibleTime,$meridian) > AdjustMeridian($startHour);
            }
        }else { //$hourMinute=="minute"
            if ($startMinute=="00" && AdjustMeridian($startHour) == AdjustMeridian2($endHour,$meridian)){
                return $possibleTime=="30";
            }else {
                return true;
            }
        }
    }
}
function AdjustMeridian($time){
    if ($time>=7 && $time<=12){
        $time -= 7;
    }else if ($time>=1 && $time<=6){
        $time += 5;
    }
    return $time;
}
function AdjustMeridian2($time,$meridian){
    if ($time==7 && $meridian=="pm"){
        return 12;
    }
    if ($time>=7 && $time<=12){
        $time -= 7;
    }else if ($time>=1 && $time<=6){
        $time += 5;
    }
    return $time;
}

function isSelected($i,$numberInput,$section){
    
    if ($section=="startHour"){
        $getSelected = $_POST['startHour'][$numberInput];
        if ($i==$getSelected){
            return true;
        }
    }else if ($section=="startMinute"){
        $getSelected = $_POST['startMinute'][$numberInput];
        if ($i==$getSelected){
            return true;
        }
    }else if ($section=="endHour"){
        $getSelected = $_POST['endHour'][$numberInput];
        if ($i==$getSelected){
            return true;
        }
    }else if ($section=="endMinute"){
        $getSelected = $_POST['endMinute'][$numberInput];
        if ($i==$getSelected){
            return true;
        }
    }
    if (isset($_POST['day_of_week']) && isset($_POST['day_of_week'][$numberInput])){
        if ($section=="day_of_week"){
            $getSelected = $_POST['day_of_week'][$numberInput];
            if ($i==$getSelected){
                return true;
            }
        }
    }
    return false;
}

function AdjustMilitary($hour,$minute){
    if ($hour>=1 && $hour<=6){
        $hour += 12;
    }else if ($hour==13){
        $hour = 19;
    }
    return "{$hour}:{$minute}:00";
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
    <style>
        .valid{
            cursor: pointer;
        }
    </style>  
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
                <div class="col-3">
                    <div class="row justify-content-around bor circ spacer text-white">
                        <h2 class="text-center text-white">Add Schedule</h2>
                        <form method="POST" class="text-white" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                            <input id="inputSubject" type="hidden" name="chosenSubject" value="">
                            <div class="form-group">
                                <label for="subj">Select Subject:</label>
                                <select class="form-control" id="subj" name="subj">
                                    <option selected disabled>Choose...</option>
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
                                            if (isset($_POST['chosenSubject']) && $_POST['chosenSubject']==$row["id"]){
                                                echo "<option value=" .$row["id"]." selected='selected'>" .$row["name"]."</option>"; 	
                                            }else{
                                                echo "<option value=" .$row["id"].">" .$row["name"]."</option>"; 	
                                            }
                                        }
                                    } else {
                                        echo "0 results";
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                            <button id="submitButton" type="submit" class="btn l-green" name="submit">Choose Subject</button>
                            <br>
                        </form>
                        <?php  if(isset($_POST['chosenSubject'])) : ?>

                            <!-- <br> -->
                            <?php 
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "mini_ismis";
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }
                                if(!isset($_POST['numberInput'])){
                                    $_POST['numberInput'] = 1;
                                }
                            ?>
                            <form id="scheduleForm" action="./schedule.php" method="POST">
                                <input type="hidden" name="chosenSubject" value="<?php echo $_POST['chosenSubject'] ?>">
                                <input type="hidden" name="showFaculty" value="0"/>
                                <?php if (isset($_POST['isDelete']) && $_POST['isDelete']!="-1") : ?>
                                <input type="hidden" name="numberInput" value="<?php echo (int)$_POST['numberInput'] ?>"/>
                                <?php else: ?>
                                <input type="hidden" name="numberInput" value="<?php echo (int)$_POST['numberInput']+1 ?>"/>
                                <?php endif ?>
                                <input type="hidden" name="isDelete" value="-1">
                                <div class="form-group text-white">
                                <?php for ($deleteIndex=-1,$j=0 ; $j<(int)$_POST['numberInput'] ; $j++) : ?>
                                    <?php 
                                        if (isset($_POST['isDelete']) && $_POST['isDelete']==$j){
                                            continue;
                                        }
                                        $deleteIndex++;
                                        if (!isset($_POST['endHour'])){
                                            $_POST['endHour'] = array("13");
                                            $_POST['endMinute'] = array("30");
                                            $_POST['startMinute'] = array("7");
                                            $_POST['startMinute'] = array("30");
                                        }else if (!isset($_POST['endHour'][$j])){
                                            $_POST['endHour'][$j] = "13";
                                            $_POST['endMinute'][$j] = "30";
                                            $_POST['startHour'][$j] = "7";
                                            $_POST['startMinute'][$j] = "30";
                                        }
                                    ?>
                                        <div class="spacer">
                                        <label for="day_of_week[]">Select Day:</label>
                                        <select class="checkConflict form-control" name="day_of_week[]" required="required">
                                            <?php $day_of_week = array("Monday","Tuesday","Wednesday","Thursday","Friday") ?>
                                            <?php for ($i=0 ; $i<5 ; $i++) : ?>
                                                <?php if (isSelected($i,$j,"day_of_week")) : ?>
                                                    <option value="<?php echo $i ?>" selected="selected"><?php echo $day_of_week[$i] ?></option>
                                                <?php else: ?>
                                                    <option value="<?php echo $i ?>"><?php echo $day_of_week[$i] ?></option>
                                                <?php endif ?>
                                            <?php endfor ?>
                                            </option>
                                        </select>
                                        <label for="day_of_week[]">Start Time: </label>
                                        <div class="form-row">
                                            <select class="selectConstraint checkConflict form-control col-md-6" name="startHour[]" required="required">
                                                <?php for ($i=7 ; TimeConstraint($i,"start","hour",$j,"am") ; $i++) : ?>
                                                    <?php if (isSelected($i,$j,"startHour")) : ?>
                                                        <option value="<?php echo $i ?>" selected="selected"><?php echo $i ?>am</option>
                                                    <?php else: ?>
                                                        <option value="<?php echo $i ?>"><?php echo $i ?>am</option>
                                                    <?php endif ?>
                                                <?php endfor ?>
                                                <?php if (TimeConstraint(12,"start","hour",$j,"12")) : ?>
                                                    <?php if (isSelected(12,$j,"startHour")) : ?>
                                                        <option value="12" selected="selected">12pm</option>
                                                    <?php else: ?>
                                                        <option value="12">12pm</option>
                                                    <?php endif ?>
                                                <?php endif ?>
                                                <?php for ($i=1 ; TimeConstraint($i,"start","hour",$j,"pm") ; $i++) : ?>
                                                    <?php if ($i==7 && isset($_POST['startHour'][$numberInput])) : ?>
                                                        <?php if (isSelected(13,$j,"startHour")) : ?>
                                                            <option value="13" selected="selected"><?php echo $i ?>pm</option>
                                                        <?php else: ?>
                                                            <option value="13"><?php echo $i ?>pm</option>
                                                        <?php endif ?>
                                                    <?php elseif ($i!=7): ?>
                                                        <?php if (isSelected($i,$j,"startHour")) : ?>
                                                            <option value="<?php echo $i ?>" selected="selected"><?php echo $i ?>pm</option>
                                                        <?php else: ?>
                                                            <option value="<?php echo $i ?>"><?php echo $i ?>pm</option>
                                                        <?php endif ?>
                                                    <?php endif ?>
                                                <?php endfor ?>
                                            </select>
                                            <select class="selectConstraint checkConflict form-control col-md-6"   name="startMinute[]" required="required">
                                                <?php if (TimeConstraint("00","start","minute",$j,"none") && isset($_POST['startHour'][$j])) : ?>
                                                    <?php if (isSelected("00",$j,"startMinute")) : ?>
                                                        <option value="00" selected="selected">00</option>
                                                    <?php else: ?>
                                                        <option value="00">00</option>
                                                    <?php endif ?>
                                                <?php endif ?>

                                                <?php if (TimeConstraint("30","start","minute",$j,"none")) :?>
                                                    <?php if (isSelected("30",$j,"startMinute")) : ?>
                                                        <option value="30" selected="selected">30</option>
                                                    <?php else: ?>
                                                        <option value="30">30</option>
                                                    <?php endif ?>
                                                <?php endif ?>
                                            </select>
                                        </div>
                                        <label for="day_of_week[]">End Time: </label>
                                        <div class="form-row">
                                            <select class="selectConstraint checkConflict form-control col-md-6" name="endHour[]" required="required">
                                                <?php 
                                                    for ($i=11 ; $i>=8 && TimeConstraint($i,"end","hour",$j,"am") ; $i--){} 
                                                    $i++;
                                                ?>
                                                <?php for ( ; $i<12 ; $i++) : ?>
                                                    <?php if (isSelected($i,$j,"endHour")) : ?>
                                                        <option value="<?php echo $i ?>" selected="selected"><?php echo $i ?>am</option>
                                                    <?php else: ?>
                                                        <option value="<?php echo $i ?>"><?php echo $i ?>am</option>
                                                    <?php endif ?>
                                                <?php endfor ?>
                                                <?php if (TimeConstraint(12,"end","hour",$j,"12")) : ?>
                                                    <?php if (isSelected(12,$j,"endHour")) : ?>
                                                        <option value="12" selected="selected">12pm</option>
                                                    <?php else: ?>
                                                        <option value="12">12pm</option>
                                                    <?php endif ?>
                                                <?php endif ?>
                                                <?php 
                                                    for ($i=7 ; $i>=1 && TimeConstraint($i,"end","hour",$j,"pm") ; $i--){} 
                                                    $i++;
                                                ?>
                                                <?php for ( ; $i<=7 ; $i++) : ?>
                                                    <?php if ($i==7) : ?>
                                                        <?php if (isSelected(13,$j,"endHour")) : ?>
                                                            <option value="13" selected="selected"><?php echo $i ?>pm</option>
                                                        <?php else: ?>
                                                            <option value="13"><?php echo $i ?>pm</option>
                                                        <?php endif ?>
                                                    <?php else: ?>
                                                        <?php if (isSelected($i,$j,"endHour")) : ?>
                                                            <option value="<?php echo $i ?>" selected="selected"><?php echo $i ?>pm</option>
                                                        <?php else: ?>
                                                            <option value="<?php echo $i ?>"><?php echo $i ?>pm</option>
                                                        <?php endif ?>
                                                    <?php endif ?>
                                                <?php endfor ?>
                                            </select>
                                            <select class="selectConstraint checkConflict form-control col-md-6" name="endMinute[]" required="required">
                                                <?php if (TimeConstraint("00","end","minute",$j,"none")) : ?>
                                                    <?php if (isSelected("00",$j,"endMinute")) : ?>
                                                        <option value="00" selected="selected">00</option>
                                                    <?php else: ?>
                                                        <option value="00">00</option>
                                                    <?php endif ?>
                                                <?php endif ?>
                                                
                                                <?php if (TimeConstraint("30","end","minute",$j,"none")) : ?>
                                                    <?php if (isSelected("30",$j,"endMinute")) : ?>
                                                        <option value="30" selected="selected">30</option>
                                                    <?php else: ?>
                                                        <option value="30">30</option>
                                                    <?php endif ?>
                                                <?php endif ?>
                                            </select>
                                        </div><br>
                                        <button class="deleteRow btn btn-danger">Delete
                                        <span class="text-danger"><?php echo $deleteIndex ?></span> </button>
                                        <hr>
                                        </div>
                                    <?php endfor ?>
                                        <input type="submit" class="btn l-green" value="Add New Schedule Time"/>
                                        </div>
                            </form>
                             <button id="showFaculty" class="btn l-green text-center">Show Available Faculty</button>
                                
                                <?php $conn->close(); ?>
                                <br/>
                                <h4 id="overlap" class="text-danger font-weight-bold"></h4>










                                <?php if (isset($_POST['showFaculty']) && $_POST['showFaculty']==1) : 
                                
                                    $servername = "localhost";
                                    $username = "root";
                                    $password = "";
                                    $dbname = "mini_ismis";
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }?>
                                    <h4>Available Faculty:</h4>
                                    <form id="facultyForm" action="./insert/route.php?tableName=schedule&insert=1" method="POST">
                                         <input type="hidden" name="chosenSubject" value="<?php echo $_POST['chosenSubject'] ?>">
                                        <?php for ($i=0 ; $i<count($_POST['day_of_week']) ; $i++) : ?>
                                            
                                            <input type="hidden" name="day_of_week[]" value="<?php echo $_POST['day_of_week'][$i] ?>"/>
                                            <input type="hidden" name="startHour[]" value="<?php echo $_POST['startHour'][$i] ?>"/>
                                            <input type="hidden" name="startMinute[]" value="<?php echo $_POST['startMinute'][$i] ?>"/>
                                            <input type="hidden" name="endHour[]" value="<?php echo $_POST['endHour'][$i] ?>"/>
                                            <input type="hidden" name="endMinute[]" value="<?php echo $_POST['endMinute'][$i] ?>"/>
                                        <?php endfor ?>
                                        <input type="hidden" name="faculty" value=""/>
                                        <ul class="list-group"><br>
                                        <?php
                                            $sql = "SELECT * FROM mini_ismis.schedules AS a
                                                    INNER JOIN mini_ismis.users AS b ON a.faculty_id=b.id AND b.user_type='FACULTY'
                                                    GROUP BY faculty_id";
                                            $result = $conn->query($sql);
                                            echo $conn->error;
                                            $isConflict = 0;
                                            while (($row = $result->fetch_assoc())):
                                                for ($i=0 ; $i<count($_POST['day_of_week']) && $isConflict==0 ; $i++){
                                                    $start = AdjustMilitary($_POST['startHour'][$i],$_POST['startMinute'][$i]);
                                                    $end = AdjustMilitary($_POST['endHour'][$i],$_POST['endMinute'][$i]);
                                                    $enumDayOfWeek = array('MON','TUE','WED','THU','FRI');
                                                    $sql2 = "SELECT * FROM mini_ismis.schedules AS a
                                                            INNER JOIN mini_ismis.schedule_session AS b ON a.id=b.schedule_id AND a.faculty_id={$row['faculty_id']}
                                                            AND '{$enumDayOfWeek[$_POST['day_of_week'][$i]]}'=b.day_of_week
                                                            AND '{$start}'<b.end
                                                            AND '{$end}'>b.start";
                                                    $result2 = $conn->query($sql2); 
                                                    if ($result2->num_rows>0){ 
                                                        $isConflict = 1;
                                                    }     
                                                }
                                                if ($isConflict==0):?>
                                                
                                                    <li class="list-group-item l-green circ text-dark valid">
                                                    <!-- <?php echo $row['id']?> | -->
                                                    <?php echo $row['first_name']." ".$row['last_name'];?></li>
                                                    <input type="hidden" value="" placeholder="<?php echo $row['id'] ?>">
                                        <?php else: 
                                                        $isConflict = 0; ?>
                                        <?php endif ?>
                                    <?php endwhile ?>
                                    <?php 
                                        $sql = "SELECT a.id AS id,a.first_name AS first_name,a.last_name AS last_name FROM mini_ismis.users AS a
                                                LEFT JOIN mini_ismis.schedules AS b ON a.id=b.faculty_id  
                                                WHERE b.faculty_id is NULL AND a.user_type='FACULTY'";
                                        $result = $conn->query($sql);
                                        while ($row=$result->fetch_assoc()) : ?>
                                            <li class="list-group-item l-green circ text-dark valid"><?php echo $row['id']?> | <?php echo $row['first_name']?> | <?php echo $row['last_name']?></li>
                                            <input type="hidden" value="" placeholder="<?php echo $row['id'] ?>">
                                <?php endwhile ?>
                                    </form>
                                <?php $conn->close(); ?>
                                <?php endif ?>
                        <?php endif ?>












                    </div>
                    <div class="row bor circ spacer justify-content-around text-white">
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
                            <select class="text-dark filterSubject" name="subject">
                                <option class="text-dark" value="none" selected="selected">None</option>
                                <option class="text-dark" value="remove">Remove</option>
                                <?php 
                                    $conn = new mysqli('localhost','root','');
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $sql = "SELECT * FROM mini_ismis.subjects";
                                    $result = $conn->query($sql);
                                    while ($row=$result->fetch_assoc()) : ?>
                                        <option class="text-dark" value="<?php echo $row['id'] ?>"
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
                            <select class="filterFaculty text-dark" name="faculty">
                                <option class="text-dark" value="none" selected="selected">None</option>
                                <option class="text-dark" value="remove">Remove</option>
                                <?php 
                                    $conn = new mysqli('localhost','root','');
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $sql = "SELECT * FROM mini_ismis.users WHERE user_type='FACULTY'";
                                    $result = $conn->query($sql);
                                    while ($row=$result->fetch_assoc()) : ?>
                                        <option class="text-dark" value="<?php echo $row['id'] ?>"
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
            $("#submitButton").on("click",function(){
                $("#inputSubject").val($("#subj").find(":selected").val());
            });
            $(".deleteRow").on("click",function(){
                    $("input[name=isDelete]").val($(this).next().html());
                    $("input[name=numberInput]").val(Number($("input[name=numberInput]").val())-1);
                    $("#scheduleForm").submit();
                });
               <?php if (isset($_POST['chosenSubject'])) : ?>
                $(".selectConstraint").on("change",function(){
                    $("input[name=numberInput]").val(Number(<?php echo $deleteIndex+1 ?>));
                    $("#scheduleForm").submit();
                });
                $("#facultyForm li").on("click",function(){
                    $("input[name=faculty]").val($(this).next().attr('placeholder'));
                    $("#facultyForm").submit();
                });
                $("#showFaculty").on("click",function(){
                    var time = [];
                    for(var i=0 ; i<$(".checkConflict").length ; i+=5){
                        index = Math.floor(i/5);
                        time[index] = [];
                        time[index][0] = $($(".checkConflict")[i]).find(":selected").text();
                        time[index][1] = $($(".checkConflict")[i+1]).find(":selected").text();
                        time[index][2] = $($(".checkConflict")[i+2]).find(":selected").text();
                        time[index][3] = $($(".checkConflict")[i+3]).find(":selected").text();
                        time[index][4] = $($(".checkConflict")[i+4]).find(":selected").text();
                    }
                    var isConflict=false;
                    for (var i=0 ; i<time.length && !isConflict ; i++){
                        for (var j=i+1 ; j<time.length ; j++){
                            if (CheckConflict(time[i],time[j])){
                                isConflict = true;
                            }
                        }
                    }
                    if (isConflict){
                        $("#overlap").html("Overlapping Schedules Exist");
                    }else {
                        $("#overlap").html("");
                        $("input[name=showFaculty]").val(1);
                        $("input[name=numberInput]").val(Number(<?php echo $deleteIndex+1 ?>));
                        $("#scheduleForm").submit();
                    }
                });
            <?php endif ?>
            $("#reset").on("click",function(){
                $("#filterOption").submit();
            });
            $(".filterSubject").on("change",function(){
                $("#formSubject").submit();
            });
            $(".filterFaculty").on("change",function(){
                $("#formFaculty").submit();
            });
        });


        function CheckConflict(baseTime,compareTime){
                var aBaseTime = AdjustTime(baseTime);
                var aCompareTime = AdjustTime(compareTime);

                if (aBaseTime.dayOfWeek == aCompareTime.dayOfWeek
                    && aBaseTime.start < aCompareTime.end
                    && aBaseTime.end > aCompareTime.start){
                        return true;
                }
                return false;
            }
            function AdjustTime(time){
                var retVal = {};

                retVal.dayOfWeek = time[0];
                retVal.start = AdjustTime2(time[1],time[2]);
                retVal.end = AdjustTime2(time[3],time[4]);
                return retVal;
            }
            function AdjustTime2(hour,minute){
                var base;
                var meridian;

                base = hour.split('');
                meridian = base.splice(-2);
                base = Number(base.join(''));
                meridian = meridian.join('');
                if (meridian=='pm'){
                    base += 12;
                }
                if (minute=='30'){
                    base += 0.5
                }
                return base;
            }
    </script>
</body>

</html>
