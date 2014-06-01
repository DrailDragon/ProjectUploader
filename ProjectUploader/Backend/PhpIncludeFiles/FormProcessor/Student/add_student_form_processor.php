<?php

/*
 * This file is generated by S. Das.
 * Do not copy it without permission.
 */
//Contact at: tapan84silchar[at]gmail.com
include '../../../../config/config.php';

session_start();
if (!isset($_SESSION['admin_user_nm'])) {
    header("Location: " . constant("HOST11") . "/Backend/login.php");
}

include '../../CommonFunctions.php';
$pageResult = "NONE";
if (isset($_POST['txtRoll'])) {
    $roll = $_POST['txtRoll'];
    $name = $_POST['txtName'];
    $user_nm = $_POST['txtUserName'];
    $class = $_POST['comboClass'];
    $advisor_id=$_POST['comboAdvisor'];
    if (($class != "MT" && $class != "BT") || !preg_match(getRollPattern(), $roll) ||
            !preg_match(getNamePattern(), $name) || !preg_match(getUserNamePattern(), $user_nm)||$advisor_id=="NONE") {
        $pageResult = "INVALID";
    } else {
        if (!($con = mysql_connect(constant("HOSTNAME"), constant("USERNAME"), constant("PASS")))) {
            $pageResult = "DBCONNECTION_ERROR";
        } else if (!($select = mysql_select_db(constant("DBNAME"), $con))) {
            $pageResult = "DBCONNECTION_ERROR";
        } else {
            $sql = "SELECT name from student where roll_number='".$roll."'";
            $rs = mysql_query($sql);
            $num_rows = mysql_num_rows($rs);
            if ($num_rows >= 1) {
                $pageResult = "EXISTS";
            } else {
                $randomPass=  generatePassword(9,4);
                $sql = "INSERT INTO student (name,user_nm,roll_number,class,password,pass_changed,pass_created_at,last_modified_by,advisor_id,permission)
                        VALUES('".$name."','".$user_nm."','".$roll."','".$class."','".$randomPass."','NO','".  date("Y-m-d")."','".$_SESSION['admin_user_nm']."[".$_SESSION['admin_name']."]','".$advisor_id."','NO')";
                $rs = mysql_query($sql);
                if (mysql_affected_rows() >= 1) {
                    $pageResult = "DONE";
                }
            }
        }
        mysql_close($con);
    }
}
$queryString="";
if($pageResult=="NONE"){
    $queryString='<br/><b>Insertion failed.....';
}else if($pageResult=="INVALID"){
    $queryString='<br/><b>Insertion failed. The data you have entered are not in valid format.';
}else if($pageResult=="EXISTS"){
    $queryString='<br/><b>Insertion failed. A student with same user name or roll number already stored.';
}else if($pageResult=="DBCONNECTION_ERROR"){
    $queryString='<br/><b>Insertion failed. Database connection error.';
}else if($pageResult=="DONE"){
    $queryString='<br/><b>Insertion successfull. Go to the student list to see the updates.';
}else{
    $queryString='<br/><b>No result';
}
$_SESSION['queryResult']=$queryString;
header("Location: " . constant("HOST11") . "/Backend/Student/student_result.php");
?>
