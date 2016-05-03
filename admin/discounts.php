<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require '../config/db.php';
require_once('../calendar/classes/tc_calendar.php');
?>

<html>    
    <head>
        <link href="../calendar/calendar.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="../calendar/calendar.js"></script>
    </head>
    
    <div id="framecontent">
        <div class='innertube'>
        <?php
            require '../nav/adminSidebar.php';
        ?>
        </div>
    </div>
    <div id="maincontent">
        <div class="innertube">
        <h2>Manage Discounts</h2>
        
        <?php 
            $qry = "Select * from discounts";
            
            $result = mysqli_query($link, $qry);

            if (!mysqli_query($link,$qry))
            {
                echo("Error description: " . mysqli_error($link));
            } else {
                if ($result->num_rows === 0) {
                    echo "You have not created any discounts yet.";
                } else {
            ?>
            <table>
                <thead>
                    <th>Discount Code</th>
                    <th>Name</th>
                    <th>Use Limit</th>
                    <th>Recurrence</th>
                    <th>Usage (C/E)</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>                        
                </thead>
            <?php
                // output data of each row
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>".$row['code'] ."</td>";
                    echo "<td>".$row['name']."</td>";                            
                    echo "<td>".$row['disclimit']."</td>";                           
                    echo "<td>".$row['recurrence']."</td>";                           
                    echo "<td>".$row['discusage']."</td>";                              
                    echo "<td>".$row['start']."</td>";                           
                    echo "<td>".$row['end']."</td>";                           
                    echo "<td>".$row['status']."</td>";                        
                    echo '<td><button onClick="window.location.href=`editDiscount.php?id='.$row['id'].'`">E</button>';
                    echo '<td><button onClick="deleteFunction('.$row['id'].')">D</button></td>';
                    echo "</tr>";
                }
            ?>
            </table>
            <?php
                } 
            }
            ?>
            <div id="updateDiscSuccess" style="color:green">
                <?php 
                    if (isset($_SESSION['updateDiscSuccess'])) {
                        echo $_SESSION['updateDiscSuccess'];
                    }
                ?>
            </div>
            <div id="updateDiscError" style="color:red">
                <?php 
                    if (isset($_SESSION['updateDiscError'])) {
                        echo $_SESSION['updateDiscError'];
                    }
                ?>
            </div>
        <hr><br>
        
        <form id='addDiscount' action='processDiscounts.php' method='post'>
            <fieldset >
            <legend>Add Discount</legend>
            <input type='hidden' name='submitted' id='submitted' value='1'/>
            <label for='code' >Discount Code*:</label>
            <input type='text' name='code' id='code' value ="<?php 
            if(isset($_SESSION['randomString'])) { 
                echo $_SESSION['randomString']; } ?>" maxlength="50" />
            <button type='button' onclick="randomString()">Generate</button>
            <br>
            <label for='name' >Name:</label>
            <input type='text' name='name' id='name'  maxlength="50" />
            <br>
            <label for='limit' >Limit*:</label>
            <input type='text' name='limit' id='limit'  maxlength="50"  onkeypress="return isNumber(event)"/>
            <br>
            <label for='recurrence' >Recurrence*:</label>
            <select name='recurrence'>
                <option value='adhoc'>Ad-hoc</option>
                <option value='weekly'>Weekly</option>
                <option value='monthly'>Monthly</option>
                <option value='yearly'>Yearly</option>
            </select>
            <br>
            <label for='usage' >Usage*:</label>
            <input type='checkbox' name='usage[]' value="cust">Customer 
            <input type='checkbox' name='usage[]' value='emp'>Employee
            <br>
            <label for='status' >Status*:</label>
            <select name='status'>
                <option value='active'>Active</option>
                <option value='inactive'>Inactive</option>
            </select>
            <br>
            Valid from:
            <?php
            $thisweek = date('W');
            $thisyear = date('Y');

            $dayTimes = getDaysInWeek($thisweek, $thisyear);
            //----------------------------------------

            $date1 = date('Y-m-d', $dayTimes[0]);
            $date2 = date('Y-m-d', $dayTimes[(sizeof($dayTimes)-1)]);

            function getDaysInWeek ($weekNumber, $year, $dayStart = 1) {
              // Count from '0104' because January 4th is always in week 1
              // (according to ISO 8601).
              $time = strtotime($year . '0104 +' . ($weekNumber - 1).' weeks');
              // Get the time of the first day of the week
              $dayTime = strtotime('-' . (date('w', $time) - $dayStart) . ' days', $time);
              // Get the times of days 0 -> 6
              $dayTimes = array ();
              for ($i = 0; $i < 7; ++$i) {
                    $dayTimes[] = strtotime('+' . $i . ' days', $dayTime);
              }
              // Return timestamps for mon-sun.
              return $dayTimes;
            }


            $myCalendar = new tc_calendar("date3", true, false);
            $myCalendar->setIcon("../calendar/images/iconCalendar.gif");
            $myCalendar->setDate(date('d', strtotime($date1)), date('m', strtotime($date1)), date('Y', strtotime($date1)));
            $myCalendar->setPath("../calendar/");
            $myCalendar->setYearInterval(1970, 2020);
            //$myCalendar->dateAllow('2009-02-20', "", false);
            $myCalendar->setAlignment('left', 'bottom');
            $myCalendar->setDatePair('date3', 'date4', $date2);
            //$myCalendar->setSpecificDate(array("2011-04-01", "2011-04-04", "2011-12-25"), 0, 'year');
            $myCalendar->writeScript();
            ?>
            to
            <?php
            $myCalendar = new tc_calendar("date4", true, false);
            $myCalendar->setIcon("../calendar/images/iconCalendar.gif");
            $myCalendar->setDate(date('d', strtotime($date2)), date('m', strtotime($date2)), date('Y', strtotime($date2)));
            $myCalendar->setPath("../calendar/");
            $myCalendar->setYearInterval(1970, 2020);
            //$myCalendar->dateAllow("", '2009-11-03', false);
            $myCalendar->setAlignment('left', 'bottom');
            $myCalendar->setDatePair('date3', 'date4', $date1);
            //$myCalendar->setSpecificDate(array("2011-04-01", "2011-04-04", "2011-12-25"), 0, 'year');
            $myCalendar->writeScript();
            ?>
            <br>
            <input type='submit' name='submit' value='Submit' />
            <div id="addDiscError" style="color:red">
                <?php 
                    if (isset($_SESSION['addDiscError'])) {
                        echo $_SESSION['addDiscError'];
                    }
                ?>
            </div>
            <p id='nanError' style="display: none;">Please enter numbers only</p>
            
            <div id="addDiscSuccess" style="color:green">
                <?php 
                    if (isset($_SESSION['addDiscSuccess'])) {
                        echo $_SESSION['addDiscSuccess'];
                    }
                ?>
            </div>
            </fieldset>
        </form>
        </div>
    </div>
    <script>
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                document.getElementById('nanError').style.display='block';
                document.getElementById('nanError').style.color='red';
                return false;
            }
            document.getElementById('nanError').style.display='none';
            return true;
        }
        function randomString() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

            for( var i=0; i < 10; i++ )
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            document.getElementById('code').value = text;
            return false;
        }
        
        function deleteFunction(locId) {
            var r = confirm("Are you sure you wish to delete this discount?");
            if (r === true) {
                window.location="processDiscounts.php?delete=1&id=" + locId;
            } else if (r === false) {
                <?php
                    unset($_SESSION['addDiscError']);
                    unset($_SESSION['addDiscSuccess']);
                    unset($_SESSION['updateDiscSuccess']);
                    $_SESSION['updateDiscError'] = "Nothing was deleted";
                ?>
                window.location='discounts.php';
            }
        }
    </script>
    
</html>

