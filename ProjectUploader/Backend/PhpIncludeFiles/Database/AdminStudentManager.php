<?php

/*
 * This file is generated by S. Das.
 * Do not copy it without permission.
 */

//Contact at: tapan84silchar[at]gmail.com
//require '../../config/config.php';

function processStudentCSVFile($file) {
    $fp = fopen($file["tmp_name"], "r");
    $arrayForStudentCorrectCSVRecord = NULL;
    $incorrectCSVRecord = "NONE";
    $indexForIncorrectCSV = 0;
    $rowIndexForCorrectCSV = 0;
    $colIndexForCorrectCSV = 0;

    $functionResult = "";
    $class1 = fgets($fp);
    $class1 = trim($class1);

    if ($class1 != "BT" && $class1 != "MT") {
        $functionResult = "MISSING_CLASS";
    } else {

        while (!feof($fp)) {
            // Reads one line at a time, up to 254 characters. Each line ends with a newline.
            // If the length argument is omitted, PHP defaults to a length of 1024.
            $line = fgets($fp);
            if (isInCorrectFormat($line)) {
                $linearray = explode(",", $line);
                $colIndexForCorrectCSV = 0;
                $arrayForStudentCorrectCSVRecord[$rowIndexForCorrectCSV][$colIndexForCorrectCSV++] = $linearray[0];
                $arrayForStudentCorrectCSVRecord[$rowIndexForCorrectCSV][$colIndexForCorrectCSV++] = $linearray[1];
                $arrayForStudentCorrectCSVRecord[$rowIndexForCorrectCSV][$colIndexForCorrectCSV++] = $linearray[2];
                $arrayForStudentCorrectCSVRecord[$rowIndexForCorrectCSV][$colIndexForCorrectCSV] = $linearray[3];
                $rowIndexForCorrectCSV++;
            } else {
                if ($incorrectCSVRecord == "NONE") {
                    $incorrectCSVRecord = "";
                }
                $incorrectCSVRecord.= '<br/>' . $line;
            }
        }

        if (count($arrayForStudentCorrectCSVRecord) > 0) {
            $functionResult = addBulkStudent($arrayForStudentCorrectCSVRecord, $class1);
        } else if ($incorrectCSVRecord != "NONE") {
            $functionResult = "FORMAT_ERROR";
        } else {
            $functionResult = "EMPTY";
        }

        if ($functionResult == "DONE" && $incorrectCSVRecord != "NONE") {
            $functionResult = "PARTIAL";
        }
    }
    session_start();
    $queryResult = "";
    if ($functionResult == "PARTIAL" || $functionResult == "UNDONE") {
        $queryResult.="<b>The following records are already available in database:</b><br/>";
        $queryResult.=$_SESSION['DuplicateRecord'];
        unset($_SESSION['DuplicateRecord']);
        $queryResult.="<br/><br/><b>The following records are incorrect in the input file:</b><br/>";
        $queryResult.=$incorrectCSVRecord;
    }
//    } else if ($functionResult == "UNDONE" && $incorrectCSVRecord != "NONE") {
//        $queryResult.="<b>The following records are already available in database:</b><br/>";
//        $queryResult.=$_SESSION['DuplicateRecord'];
//        unset($_SESSION['DuplicateRecord']);
//        $queryResult.="<br/><br/><b>The following records are incorrect in the input file:</b><br/>";
//        $queryResult.=$incorrectCSVRecord;
//    }
    $_SESSION['message'] = $queryResult;
    return $functionResult;
}

function addBulkStudent($studentRecords, $class) {
    $result;
    $DuplicateCSVRecords = "";
    if (!($con = mysql_connect(constant("HOSTNAME"), constant("USERNAME"), constant("PASS")))) {
        $result = "DBCONNECTION_ERROR";
    } else if (!($select = mysql_select_db(constant("DBNAME"), $con))) {
        $result = "DBCONNECTION_ERROR";
    } else {
        $totalRecord = count($studentRecords);
        $recordIndex = 0;

        $result = "UNDONE";
        $createdAt = date("Y-m-d H:i:s");
        for ($recordIndex = 0; $recordIndex < $totalRecord; $recordIndex++) {
            $randomPass = generatePassword(9, 4);
            $v1 = $studentRecords[$recordIndex][0];
            $v2 = $studentRecords[$recordIndex][1];
            $v3 = $studentRecords[$recordIndex][2];
            $v4 = $studentRecords[$recordIndex][3];
            $sql = "INSERT INTO student(roll_number,name,user_nm,password,class,pass_changed,pass_created_at,last_modified_by,advisor_id,permission)
                   VALUES('$v1','$v2','$v3','$randomPass','$class','NO','$createdAt','" . $_SESSION[admin_name] . "[" . $_SESSION['admin_user_nm'] . "]','$v4','NO')";
            mysql_query($sql);
            //$row = mysql_fetch_assoc($result);
            if (mysql_affected_rows() >= 1) {
                if ($result == "UNDONE") {
                    $result = "DONE";
                }
            } else {
                $DuplicateCSVRecords = $DuplicateCSVRecords . '<br/>' . $v1 . "," . $v2 . ',' . $v3. ',' . $v4;
                if ($result == "DONE") {
                    $result = "PARTIAL";
                }
            }
        }
    }
    mysql_close($con);
    session_start();
    if ($result == "PARTIAL" || $result == "UNDONE") {
        $_SESSION['DuplicateRecord'] = $DuplicateCSVRecords;
    }
    return $result;
}

function isInCorrectFormat($line) {
    $linearray = explode(",", $line);
    if (preg_match(getRollPattern(), $linearray[0]) && preg_match(getNamePattern(), $linearray[1]) && preg_match(getUserNamePattern(), $linearray[2])&& preg_match(getUserNamePattern(), $linearray[3])) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function getAllStudents($class) {
    $result;
    if (!($con = mysql_connect(constant("HOSTNAME"), constant("USERNAME"), constant("PASS")))) {
        $result = "DBCONNECTION_ERROR";
    } else if (!($select = mysql_select_db(constant("DBNAME"), $con))) {
        $result = "DBCONNECTION_ERROR";
    } else {
        $sql = "SELECT name,roll_number,user_nm,password from student where class='" . $class . "'";
        $result = mysql_query($sql);
        $flag = FALSE;
        $counter = 0;
        $inerhtml = ' <table border="0" align="center" style="height: 100%; width: 100%">
                       <tr>
                                <td style="width: 10px;color: #990000">S.N.</td>
                                <td style="width: 250px;text-align: center;color: #990000">Name</td>
                                <td style="width: 80px; text-align: center;color: #990000">Roll</td>
                                <td style="width: 50px; text-align: center;color: #990000">Class</td>
                                <td style="width: 200px;text-align: center;color: #990000">User Name</td>
                                <td style="width: 150px;text-align: center;color: #990000">Password</td>
                            </tr>';
        while ($row = mysql_fetch_assoc($result)) {
            $flag = TRUE;
            $counter++;
            if ($class == "BT") {
                $fullClass = "B.Tech";
            } else {
                $fullClass = "M.Tech";
            }

            $inerhtml = $inerhtml . '<tr style="background-color: menu;color: black">';
            $inerhtml = $inerhtml . '<td style="width: 10px" align="center">' . $counter . '</td>';
            $inerhtml = $inerhtml . '<td style="width: 250px" align="center">' . $row["name"] . '</td>';
            $inerhtml = $inerhtml . '<td style="width: 80px" align="center"><a href="' . constant("HOST11") . '/Backend/Student/student_edit.php?roll=' . $row['roll_number'] . '">' . $row['roll_number'] . '</a></td>';
            $inerhtml = $inerhtml . '<td style="width: 50px" align="center">' . $fullClass . '</td>';
            $inerhtml = $inerhtml . '<td style="width: 200px" align="center">' . $row["user_nm"] . '</td>';
            $inerhtml = $inerhtml . '<td style="width: 150px" align="center">' . $row["password"] . '</td>';
            $inerhtml = $inerhtml . '</tr>';
            $inerhtml = $inerhtml . '<tr><td colspan="6" style="height:2px;background-color: gray"></td></tr>';
        }
        $inerhtml = $inerhtml . '</table>';
        if ($flag == TRUE) {
            $result = "DONE";
			if(!isset($_SESSION)){
            session_start();
			}
            $_SESSION['innerHTMLSimple'] = $inerhtml;
       } else {
            $result = "NOT_FOUND";
        }
    }
    mysql_close($con);
    return $result;
}

function getAllFaculties() {
    $result = "NONE";
    if (!($con = mysql_connect(constant("HOSTNAME"), constant("USERNAME"), constant("PASS")))) {
        $result = "DBCONNECTION_ERROR";
    } else if (!($select = mysql_select_db(constant("DBNAME"), $con))) {
        $result = "DBCONNECTION_ERROR";
    } else {
        $sql = "SELECT advisor_name,advisor_id from advisor order by advisor_name";
        $sql_result = mysql_query($sql);
        $flag = FALSE;
        $inerhtml = '';
        while ($row = mysql_fetch_assoc($sql_result)) {
            $flag = TRUE;
            if ($row["advisor_name"] != "Admin") {
                $inerhtml = $inerhtml . '<option value="' . $row["advisor_id"] . '">' . $row["advisor_name"] . '</option>';
            }
        }
        if ($flag == TRUE) {
            $result = "DONE";
            session_start();
            $_SESSION['innerHTMLSimple'] = $inerhtml;
        } else {
            $result = "NOT_FOUND";
        }
    }
    mysql_close($con);
    return $result;
}

function getAllFacultiesForEditForm($advisor_id) {
    $result = "NONE";
    if (!($con = mysql_connect(constant("HOSTNAME"), constant("USERNAME"), constant("PASS")))) {
        $result = "DBCONNECTION_ERROR";
    } else if (!($select = mysql_select_db(constant("DBNAME"), $con))) {
        $result = "DBCONNECTION_ERROR";
    } else {
        $sql = "SELECT advisor_name,advisor_id from advisor order by advisor_name";
        $sql_result = mysql_query($sql);
        $flag = FALSE;
        $inerhtml = '';
        while ($row = mysql_fetch_assoc($sql_result)) {
            $flag = TRUE;
            if ($row["advisor_name"] != "Admin") {
                if ($row["advisor_id"] == $advisor_id) {
                    $inerhtml = '<option value="' . $row["advisor_id"] . '">' . $row["advisor_name"] . '</option>' . $inerhtml;
                } else {
                    $inerhtml = $inerhtml . '<option value="' . $row["advisor_id"] . '">' . $row["advisor_name"] . '</option>';
                }
            }
        }
        if ($flag == TRUE) {
            $result = "DONE";
            session_start();
            $_SESSION['innerHTMLSimple'] = $inerhtml;
        } else {
            $result = "NOT_FOUND";
        }
    }
    mysql_close($con);
    return $result;
}

?>
