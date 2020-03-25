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

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "mini_ismis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
    }
    $sql = "DELETE FROM schedules WHERE faculty_id=" .$_GET['faculty_id']." AND subject_id= ".$_GET['subject_id'];
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        header("Location: faculty.php");
    }
    $sql = "DELETE FROM subject_faculty WHERE faculty_id=" .$_GET['faculty_id']." AND subject_id= ".$_GET['subject_id'];
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        header("Location: faculty.php");
    }

    $sql = "DELETE FROM schedules WHERE id=" .$_GET['id'];
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        header("Location: faculty.php");
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
    $compile = '';
    if (isset($_POST['filterFaculty']) && $_POST['filterFaculty']!='None'){
        $compile .= "AND faculty_id={$_POST['filterFaculty']}";
    }else if (isset($_POST['filterSubject']) && $_POST['filterSubject']!='None'){
        $compile .= "AND subject_id={$_POST['filterSubject']}";
    }
    $sql = "SELECT distinct users.id as fac, users.first_name,users.last_name, subjects.name,subjects.id AS sub_id from subject_faculty
    inner join users
    on faculty_id=users.id
    inner join subjects
    on subject_id=subjects.id {$compile}
    order by users.first_name ASC";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table class=\"table text-white\"><tr><th>Faculty</th><th>Subject</th>";
        // <th>Edit</th>
        echo "<th>Delete</th></tr>";
            while ($row = $result->fetch_assoc()){
                echo "<tr><td>".$row["first_name"]." " .$row["last_name"]."</td>";
                echo "<td>".$row["name"]."</td>";
                // echo "<td><a href=\"../update.php?info=users&id=".$row["fac"]."\"><button class=\"btn btn-info btn-xs\" data-title=\"Edit\" data-toggle=\"modal\" data-target=\"#edit\" ><span class=\"glyphicon glyphicon-pencil\"></span></button></p></a></td>";
                echo "<td><a href=\"faculty.php?action=delete&faculty_id=".$row["fac"]."&subject_id=".$row["sub_id"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
                echo '</tr>';
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
    <title>minIsmis - Faculty</title>
</head>
<<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="../style.css" />
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>  -->

<body class="container bg-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-success circ spacer">
        <a class="navbar-brand title" href="../index.php">min<span class="white">Ismis</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../schedule.php"><span>Schedule</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../subjects.php"><span>Subjects</span></a>
                </li>
                <?php if ($_SESSION['type']==="ADMIN") : ?>
                    <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span>People</span>
        </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="faculty.php">Faculty</a>
                        <a class="dropdown-item" href="students.php">Students</a>
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
                            echo "<a class=\"dropdown-item\" href=\"profile.php?id=" .$_SESSION["id"]. "\">View Account</a>";
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
            <div class="col-4 bg-success text-white circ">
                <h2 class="text-center">Filter</h2>
                






                    <form id="filterRadio" action="./faculty.php" method="POST">
                    <?php
                        if (!isset($_POST['filterSubject'])) : ?>
                            <input type="radio" id="filterFacultyRadio" name="filter" checked="checked"/>
                            <label for="filterFacultyRadio">Faculty</label>
                            <input type="radio" id="filterSubjectRadio" name="filter"/>
                            <label for="filterSubjectRadio">Subject</label>
                  <?php else: ?>
                            <input type="radio" id="filterFacultyRadio" name="filter"/>
                            <label for="filterFacultyRadio">Faculty</label>
                            <input type="radio" id="filterSubjectRadio" name="filter" checked="checked"/>
                            <label for="filterSubjectRadio">Subject</label>
                  <?php endif ?>
                    <input type="radio" id="reset" name="filter"/>
                    <label for="reset">Reset</label>
                    </form>

                    <form id="filterFacultyOption" action="./faculty.php" method="POST">
                        <label for="filterFaculty">Select Faculty:</label>
                        <select class="text-dark filterFacultyOption text-dark" id="selectFaculty" name="filterFaculty">
                            <option>None</option>
                            <?php
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "mini_ismis";
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                $sqlFilter="SELECT * FROM mini_ismis.users WHERE user_type='FACULTY'";
                                $resultFilter = $conn->query($sqlFilter);
                                while ($rowFilter=$resultFilter->fetch_assoc()) : ?>
                                <?php if (isset($_POST['filterFaculty']) && $_POST['filterFaculty']==$rowFilter['id']) : ?>
                                    <option value="<?php echo $rowFilter['id'] ?>" selected="selected">
                                <?php else: ?>
                                    <option value="<?php echo $rowFilter['id'] ?>">
                                <?php endif ?>
                                        <?php echo $rowFilter['first_name']. " " .$rowFilter['last_name'] ?>
                                    </option>
                        <?php endwhile; 
                            $conn->close();
                        ?>
                        
                        </select>
                    </form>
                    <form id="filterSubjectOption" action="./faculty.php" method="POST">
                    <label for="filterSubject">Select Subject:</label>
                        <select class="text-dark filterSubjectOption" id="selectSubject" name="filterSubject">
                            <option class="text-dark" >None</option>
                            <?php
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "mini_ismis";
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                $sqlFilter="SELECT * FROM mini_ismis.subjects";
                                $resultFilter = $conn->query($sqlFilter);
                                while ($rowFilter=$resultFilter->fetch_assoc()) : ?>
                                    <?php if (isset($_POST['filterSubject']) && $_POST['filterSubject']==$rowFilter['id']) : ?>
                                        <option class="text-dark" value="<?php echo $rowFilter['id'] ?>" selected="selected">
                                    <?php else: ?>
                                        <option class="text-dark" value="<?php echo $rowFilter['id'] ?>">
                                    <?php endif ?>
                                        <?php echo $rowFilter['name'] ?> 
                                    </option>
                        <?php endwhile; 
                            $conn->close();
                        ?>
                        
                        </select>
                    </form>








                <?php
                if(isset($_POST['AddEmp'])){
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "mini_ismis";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                    }
                    $sql = "INSERT INTO schedules (subject_id, faculty_id) VALUES ('".$_POST['subj']."','".$_POST['facu']."')";
                    if (!mysqli_query($conn, $sql)) {
                    // } else {
                        echo "Error: Duplicate Entry";
                    }
                    $conn->close();
                }
                ?>
            </div>
            <div class="col-<?php echo ($_SESSION['type']==="ADMIN")? "8": "12";?> bg-success text-white circ">
                <h2 class="text-center">Faculty</h2>
                <?php
                    // if($_SESSION['type']=="ADMIN"){
                        admin_tabler();
                    // }else{
                        // stud_tabler();
                    // }
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
            console.log("dasd");
        $(document).ready(function(){
            if ($("#filterFacultyRadio").is(":checked")){
            console.log("dasd");
                $("#filterFacultyOption").css("display","block");
                $("#filterSubjectOption").css("display","none");
            }else {
                $("#filterFacultyOption").css("display","none");
                $("#filterSubjectOption").css("display","block");
            }
            $("input[type=radio]").on("click",function(){
                if ($("#filterFacultyRadio").is(":checked")){
                    $("#filterFacultyOption").css("display","block");
                    $("#filterSubjectOption").css("display","none");
                }else {
                    $("#filterFacultyOption").css("display","none");
                    $("#filterSubjectOption").css("display","block");
                }
            });
            $("#reset").on("click",function(){
                $("#filterRadio").submit();
            });
            $("#selectFaculty").on("change",function(){
                $("#filterFacultyOption").submit();
            });
            $("#selectSubject").on("change",function(){
                $("#filterSubjectOption").submit();
            });
        });
    </script>
</body>

</html>
