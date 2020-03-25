<?php
    session_start();
    $conn = new mysqli('localhost','root','');

    if ($conn->connect_error){
        die('Connection Failed: '.$conn->connect_error);
    }

    function AdjustMilitary($hour,$minute){
        if ($hour>=1 && $hour<=6){
            $hour += 12;
        }else if ($hour==13){
            $hour = 19;
        }
        return "{$hour}:{$minute}:00";
    }

    if (isset($_GET['tableName'])){
        if (isset($_GET['insert']) && $_GET['insert']=='1'){
            if (isset($_POST['subject']) && isset($_POST['student']) && isset($_POST['schedule'])){
                $sql = "INSERT INTO mini_ismis.subject_student(subject_id,student_id) VALUES
                                                                ({$_POST['subject']},{$_POST['student']})";
                if ($result=$conn->query($sql)==false){
                    die($conn->error);
                }
                $sql = "INSERT INTO mini_ismis.classes(schedule_id,student_id) VALUES
                                                        ({$_POST['schedule']},{$_POST['student']})";
                if ($result=$conn->query($sql)==false){
                    die($sql);
                }
                if ($_SESSION['type']=='ADMIN'){
                    header("Location: ../users/students.php");
                }else {
                    header("Location: ../subjects.php");
                }
                exit();
            }
            if (isset($_POST['faculty']) && isset($_POST['chosenSubject']) && isset($_POST['day_of_week']) && isset($_POST['startHour'])
                && isset($_POST['startMinute']) && isset($_POST['endHour']) && isset($_POST['endMinute'])){
                    $faculty = $_POST['faculty'];
                    $subject = $_POST['chosenSubject'];
                    $sql = "INSERT INTO mini_ismis.schedules(subject_id,faculty_id) VALUES({$subject},{$faculty})";
                    if($conn->query($sql)==false){
                        die($conn->error);
                    }
                    $insertId = $conn->insert_id;
                    for ($i=0 ; $i<count($_POST['day_of_week']) ; $i++){
                        $start = AdjustMilitary($_POST['startHour'][$i],$_POST['startMinute'][$i]);
                        $end = AdjustMilitary($_POST['endHour'][$i],$_POST['endMinute'][$i]);
                        $enumDayOfWeek = array('MON','TUE','WED','THU','FRI');
                        $dayOfWeek = $enumDayOfWeek[$_POST['day_of_week'][$i]];

                        $sql = "INSERT INTO mini_ismis.schedule_session(schedule_id,day_of_week,start,end) VALUES(
                                                {$insertId},'{$dayOfWeek}','{$start}','{$end}')";
                        if($conn->query($sql)==false){
                            die($conn->error);
                        }
                    }
                    $sql = "SELECT * FROM mini_ismis.subject_faculty WHERE subject_id={$subject} AND faculty_id={$faculty}";
                    $record = $conn->query($sql);
                    if($record==false){
                        die($conn->error);
                    }
                    if ($record->num_rows==0){
                        $sql = "INSERT INTO mini_ismis.subject_faculty(faculty_id,subject_id) VALUES({$faculty},{$subject})";
                    }
                    header("Location: ../schedule.php");
                    exit();
                }
        }else {
            if ($_GET['tableName']=='subject_student'){
                header("Location: ./subject_student.php");
                exit();
            }else if($_GET['tableName']=='schedule' && isset($_GET['subject'])){
                header("Location: ./schedule.php");
                exit();
            }
        }
    }
?>
<script>
// javascript:history.go(-1)
</script>
