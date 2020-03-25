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
    $sql = "SELECT DISTINCT users.id as stu, users.first_name, users.last_name, subjects.name FROM `classes`
    INNER JOIN users
    on student_id=users.id
    INNER JOIN schedule_session
    on classes.schedule_id=schedule_session.schedule_id
    INNER JOIN schedules
    on schedules.id=schedule_session.schedule_id
    INNER JOIN subjects
    on subjects.id=schedules.subject_id
    order by users.id ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table class=\"table text-white\"><tr><th>Student</th><th>Subject</th><th>Edit</th><th>Delete</th></tr>";
            while ($row = $result->fetch_assoc()){
                echo "<tr><td>".$row["first_name"]." " .$row["last_name"]."</td>";
                echo "<td>".$row["name"]."</td>";
                echo "<td><a href=\"../update.php?info=users&id=".$row["stu"]."\"><button class=\"btn btn-info btn-xs\" data-title=\"Edit\" data-toggle=\"modal\" data-target=\"#edit\" ><span class=\"glyphicon glyphicon-pencil\"></span></button></p></a></td>";
                echo "<td><a href=\"student.php?action=delete&id=".$row["stu"]."\"><button class=\"btn btn-danger btn-xs\" data-title=\"Delete\" data-toggle=\"modal\" data-target=\"#delete\" ><span class=\"glyphicon glyphicon-trash\"></span></button></p></td>";
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
    <title>minIsmis - Students</title>

    <style>
        .invalid{
            color:red;
        }
        .valid{
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
            integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link rel="stylesheet" type="text/css" href="../style.css" />
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>  -->
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
                <h2 class="text-center">Enroll Student</h2>
                <form id="studentForm" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                    <input id="studentInput" type="hidden" name="student" value="">
                    <div class="form-group">
                            <label for="facu">Select Student:</label>
                            <select class="form-control" id="facu" name="facu" required>
                                <?php if (!isset($_POST['student'])) : ?>
                                    <option selected disabled>Choose...</option>
                                <?php endif ?>
                                <?php
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "mini_ismis";
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                $sql = "SELECT * FROM users where user_type='STUDENT'";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        if (isset($_POST['student']) && $_POST['student']==$row['id']){
                                            echo "<option value=" .$row["id"].". selected='selected'>" .$row["first_name"]. " ".$row["last_name"]."</option>";
                                        }else {
                                            echo "<option value=" .$row["id"].">" .$row["first_name"]. " ".$row["last_name"]."</option>";
                                        }
                                    }
                                } else {
                                    echo "0 results";
                                }
                                $conn->close();
                                ?>
                            </select>
                    </div>
                </form>
                <?php if (isset($_POST['student']) && $_POST['student']!="Choose...") : ?>
                    <form id="subjectForm" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                        <input id="studentInput2" type="hidden" name="student" value="<?php echo $_POST['student'] ?>"/>
                        <input id="subjectInput2" type="hidden" name="subject" value=""/>
                        <div class="form-group">
                                <label for="subj">Select Subject:</label>
                                <select class="form-control" id="subj" name="subj" required>
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

                                    $sql = "SELECT b.name AS name,b.id AS id FROM mini_ismis.subject_student AS a
                                    RIGHT JOIN mini_ismis.subjects AS b ON a.subject_id=b.id AND a.student_id={$_POST['student']}
                                    WHERE student_id IS NULL";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            if (isset($_POST['subject']) && $_POST['subject']==$row['id']){
                                                echo "<option value=" .$row["id"]." selected='selected'>" .$row["name"]."</option>"; 
                                            }else {
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
                        <button id="subjectButton" type="submit" class="btn l-green">Enroll Student</button>
                    </form>
                    <br>
                <?php endif ?>
                <?php
                if(isset($_POST['student']) && isset($_POST['subject'])
                    && $_POST['student']!="Choose..." && $_POST['subject']!="Choose...") : 
                    
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "mini_ismis";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    
                    ?>
                    <form id="enrollStudentForm" action="./insert/subject_student.php" method="POST">
                        <input type="hidden" name="student" value="<?php echo $_POST['student'] ?>">
                        <ul class="list-group">
                        <h2>Available Schedules for</h2>
                        <?php
                        $sql5 = "SELECT maximum_population FROM mini_ismis.subjects WHERE id={$_POST['subject']}";
                        $result5 = $conn->query($sql5);
                        $row5 = $result5->fetch_assoc();
                        $sql = "SELECT * FROM mini_ismis.schedules WHERE subject_id={$_POST['subject']}";
                        $result = $conn->query($sql);
                        while ($row=$result->fetch_assoc()) : 
                            $sql6 = "SELECT COUNT(*) FROM mini_ismis.classes WHERE schedule_id={$row['id']}";
                            $result6 = $conn->query($sql6);
                            $row6 = $result6->fetch_assoc();
                            $sql4 = "SELECT * FROM mini_ismis.schedule_session WHERE schedule_id={$row['id']}";
                            $result4 = $conn->query($sql4);
                            $isConflict = 0;
                            while (($row4=$result4->fetch_assoc()) && $isConflict==0){
                                
                                $sql2 = "SELECT * FROM mini_ismis.classes AS a
                                INNER JOIN mini_ismis.schedules AS b ON a.schedule_id=b.id AND a.student_id={$_POST['student']}
                                INNER JOIN mini_ismis.schedule_session AS c ON c.schedule_id=b.id 
                                AND '{$row4['start']}'<c.end AND '{$row4['end']}'>c.start AND {$row4['id']}!=c.id AND '{$row4['day_of_week']}'=c.day_of_week";
                                
                                $result2 = $conn->query($sql2);
                                if ($result2->num_rows>0){
                                    $isConflict = 1;
                                }
                            }
                            $result4->data_seek(0);
                            if ($isConflict==1 || $row6['COUNT(*)']>=$row5['maximum_population']) : 
                                $isConflict = 0; ?>
                                <li class="invalid list-group-item disabled circ">
                                Group Number: <?php echo $row['id'] ?>&ensp;<br>
                          <?php while ($row4=$result4->fetch_assoc()) : ?>
                                    <?php echo $row4['day_of_week'] ?>&ensp;<?php echo $row4['start'] ?>-<?php echo $row4['end'] ?>&emsp;<br>
                          <?php endwhile ?>
                      <?php else: ?>
                                <li class="valid list-group-item  bg-success text-white circ">
                                Group Number: <?php echo $row['id'] ?>&ensp;<br>
                          <?php while ($row4=$result4->fetch_assoc()) : ?>
                                    <?php echo $row4['day_of_week'] ?>&ensp;<?php echo $row4['start'] ?>-<?php echo $row4['end'] ?>&emsp;<br>
                          <?php endwhile ?>
                      <?php endif ?>
                      &emsp;Population: <?php echo $row6['COUNT(*)'] ." ". $row5['maximum_population'] ?>
                            </li>
                            <input type="hidden" value="" placeholder="<?php echo $row['id'] ?>">
                            <br/>
                            <br/>
                  <?php endwhile ?>
                  </ul>
                </form>
            <?php endif ?>
            </div>
            <div class="col-<?php echo ($_SESSION['type']==="ADMIN")? "8": "12";?> bg-success text-white circ">
                <h2 class="text-center">Students</h2>
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
        $(document).ready(function(){
            $("#facu").on("change",function(){
                var student = $("#facu option").filter(":selected").val();
                var text = $("#facu option").filter(":selected").text();
                    $("#studentInput").val(student);
                    $("#studentForm").submit();
            });
            $("#subjectForm").submit(function(event){
                event.preventDefault();
                var subject = $("#subj option").filter(":selected").val();
                var text = $("#subj option").filter(":selected").text();
                $("#subjectInput2").val(subject);
                $(this).unbind('submit').submit();
            });
            $(".valid").on("click",function(){
                $("input[name=schedule]").val($(this).next().attr('placeholder'));
                $("#enrolStudentForm").submit();
            });
        });
    </script>
</body>

</html>
