<?php

/*
 * This file is generated by S. Das.
 * Do not copy it without permission.
 */
//Contact at: tapan84silchar[at]gmail.com
function getAllSchedules(){
    $result;
    if (!($con = mysql_connect(constant("HOSTNAME"), constant("USERNAME"), constant("PASS")))) {
        $result = "DBCONNECTION_ERROR";
    } else if (!($select = mysql_select_db(constant("DBNAME"), $con))) {
        $result = "DBCONNECTION_ERROR";
    } else {
        $sql = "SELECT class,last_date  FROM schedule";
        $result = mysql_query($sql);
        $flag = FALSE;
        $counter=0;
        $inerhtml = ' <table border="0" align="center" style="height: 100%; width: 90%">
                       <tr>
                                <td style="width: 10px;color: #990000">S.N.</td>
                                <td style="width: 250px;text-align: center;color: #990000">Class Name</td>
                                <td style="width: 250px; text-align: center;color: #990000">Last Date Of Submission</td>
                       </tr>
                       <tr><td colspan="3">Click on the Class to change last submission date.</td></tr>';
        while ($row = mysql_fetch_assoc($result)) {
            $flag = TRUE;
            $counter++;
            $class=$row['class'];
            if($class=="BT"){
                $fullClass="B.Tech";
            }else{
                $fullClass="M.Tech";
            }
            //$dateArray=explode("-",$row['last_date']);
            //$phpDate=$dateArray[2].'/'.$dateArray[1].'/'.$dateArray[0];
            
            $inerhtml = $inerhtml . '<tr style="background-color: menu;color: black">';
            $inerhtml = $inerhtml . '<td style="width: 10px" align="center">' . $counter . '</td>';
            $inerhtml = $inerhtml . '<td style="width: 250px" align="center"><a href="'.constant("HOST11").'/Backend/Schedule/schedule_edit.php?class='.$class.'">'.$fullClass.'</a></td>';
            $inerhtml = $inerhtml . '<td style="width: 250px" align="center">' .date("d-F-Y",strtotime($row['last_date'])).'</td>';
            $inerhtml = $inerhtml . '</tr>';
            $inerhtml = $inerhtml . '<tr><td colspan="6" style="height:2px;background-color: gray"></td></tr>';
        }
        $inerhtml = $inerhtml . '</table>';
        if ($flag == TRUE) {
            $result = "DONE";
			if(!isset($_SESSION))
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
