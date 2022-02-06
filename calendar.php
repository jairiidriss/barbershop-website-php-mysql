<?php
    
    //PHP INCLUDES

	include "connect.php";

	if(isset($_POST['selected_employee']) && isset($_POST['selected_services']))
	{

		?>

        <!-- CALENDAR STYLE -->
        
        <style type="text/css">
            
            .calendar_tab
            {
                background: white;
                margin-top: 5px;
                width: 100%;
                position: relative;
                box-shadow: rgba(60, 66, 87, 0.04) 0px 0px 5px 0px, rgba(0, 0, 0, 0.04) 0px 0px 10px 0px;
                overflow: hidden;
                border-radius: 4px;
            }

            .appointment_day
            {
                width: 15%;
                text-align: center;
                display: flex;
                color: rgb(151, 151, 151);
                font-weight: 700;
                -webkit-box-align: center;
                align-items: center;
                -webkit-box-pack: center;
                justify-content: center;
                font-size: 14px;
                line-height: 1.5;
            }

            .appointments_days
            {
                border-top-left-radius: 4px;
                border-top-right-radius: 4px;
                display: flex;
                height: 60px;
                position: relative;
                -webkit-box-pack: justify;
                justify-content: space-between;
                padding: 10px;
                border-bottom: 1px solid rgb(229, 229, 229);
            }

            .available_booking_hours
            {
                display: flex;
                -webkit-box-pack: justify;
                justify-content: space-between;
                padding: 10px;
                border-radius: 4px;
            }

            .available_booking_hour:hover
            {
                font-weight: 700;
            }

            .available_booking_hour
            {
                font-size: 14px;
                padding-top:25px;
                line-height: 1.3;
                cursor: pointer;
            }


            input[type="radio"] 
            {
                display: none;
            }

            input[type="radio"]:checked + label 
            {
                font-weight: 700;
            }

            .available_booking_hours_colum
            {
                width: 15%;
                text-align: center;
            }

        </style>

        <!-- END CALENDAR STYLE -->

        <!-- START CALENDAR SLOT -->

        <div class="calendar_slots" style="min-width: 600px;">

            <!-- NEXT 10 DAYS -->

            <div class="appointments_days">
                <?php
                    
                    $appointment_date = date('Y-m-d');

                    for($i = 0; $i < 10; $i++)
                    {
                        $appointment_date = date('Y-m-d', strtotime($appointment_date . ' +1 day'));
                        echo "<div class = 'appointment_day'>";
                            echo date('D', strtotime($appointment_date));
                            echo "<br>";
                            echo date('d', strtotime($appointment_date))." ".date('M', strtotime($appointment_date));
                        echo "</div>";
                    } 
                ?>
            </div>

            <!-- DAY HOURS -->

            <div class = 'available_booking_hours'>
                <?php

                    //SELECTED SERVICES
		            $desired_services = $_POST['selected_services'];
		            
                    //SELECTED EMPLOYEE
		            $selected_employee = $_POST['selected_employee'];

            		//Services Duration - End time expected
		            $sum_duration = 0;
		            
                    foreach($desired_services as $service)
		            {
		                
		                $stmtServices = $con->prepare("select service_duration from services where service_id = ?");
		                $stmtServices->execute(array($service));
		                $rowS =  $stmtServices->fetch();
		                $sum_duration += $rowS['service_duration'];
		                
		            }
            
            
		            $sum_duration = date('H:i',mktime(0,$sum_duration));
		            $secs = strtotime($sum_duration)-strtotime("00:00:00");


                    $open_time = date('H:i',mktime(9,0,0));

                    $close_time = date('H:i',mktime(22,0,0));

                    $start = $open_time;

                    $secs = strtotime($sum_duration)-strtotime("00:00:00");
                    $result = date("H:i:s",strtotime($start)+$secs);


                    $appointment_date = date('Y-m-d');

                    for($i = 0; $i < 10; $i++)
                    {
                        echo "<div class='available_booking_hours_colum'>";

                            $appointment_date = date('Y-m-d', strtotime($appointment_date . ' +1 day'));
                            $start = $open_time;
                            $secs = strtotime($sum_duration)-strtotime("00:00:00");
                            $result = date("H:i:s",strtotime($start)+$secs);

                            $day_id = date('w',strtotime($appointment_date));
                            
                            while($start >= $open_time && $result <= $close_time)
                            {
                                // Check If the employee is available

                                $stmt_emp = $con->prepare("
                                    Select employee_id
                                    from employees_schedule
                                    where employee_id = ?
                                    and day_id = ?
                                    and ? between from_hour and to_hour
                                    and ? between from_hour and to_hour
                                       
                                ");
                                $stmt_emp->execute(array($selected_employee,$day_id,$start, $result));
                                $emp = $stmt_emp->fetchAll();

                                //If employee is available

                                if($stmt_emp->rowCount() != 0)
                                {

                                    //Check If there are no intersecting appointments with the current one
                                    $stmt = $con->prepare("
                                        Select * 
                                        from appointments a
                                        where
                                            date(start_time) = ?
                                            and
                                            a.employee_id = ?
                                            and
                                            canceled = 0
                                            and
                                            (   
                                                time(start_time) between ? and ?
                                                or
                                                time(end_time_expected) between ? and ?
                                            )
                                    ");
                                    
                                    $stmt->execute(array($appointment_date,$selected_employee,$start,$result,$start,$result));
                                    $rows = $stmt->fetchAll();
                        
                                    if($stmt->rowCount() != 0)
                                    {
                                        //Show blank cell
                                    }
                                    else
                                    {
                                        ?>
                                            <input type="radio" id="<?php echo $appointment_date." ".$start; ?>" name="desired_date_time" value="<?php echo $appointment_date." ".$start." ".$result; ?>">
                                            <label class="available_booking_hour" for="<?php echo $appointment_date." ".$start; ?>"><?php echo $start; ?></label>
                                        <?php
                                    }
                                }
                                else
                                {
                                    //Show Blank cell
                                }
                                

                                $start = strtotime("+15 minutes", strtotime($start));
                                $start =  date('H:i', $start);

                                $secs = strtotime($sum_duration)-strtotime("00:00:00");
                                $result = date("H:i",strtotime($start)+$secs);
                            }

                        echo "</div>";
                    }
                ?>
            </div>
        </div>
	<?php
	}
    else
    {
        header('location: index.php');
        exit();
    }
?>