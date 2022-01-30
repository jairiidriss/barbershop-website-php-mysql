<!-- PHP INCLUDES -->

<?php

    include "connect.php";
    include "Includes/functions/functions.php";
    include "Includes/templates/header.php";
    include "Includes/templates/navbar.php";

?>
<!-- Appointment Page Stylesheet -->
<link rel="stylesheet" href="Design/css/appointment-page-style.css">

<!-- BOOKING APPOINTMENT SECTION -->

<section class="booking_section">
	<div class="container">

		<?php

            if(isset($_POST['submit_book_appointment_form']) && $_SERVER['REQUEST_METHOD'] === 'POST')
            {
            	// Selected SERVICES

                $selected_services = $_POST['selected_services'];

                // Selected EMPLOYEE

                $selected_employee = $_POST['selected_employee'];

                // Selected DATE+TIME

                $selected_date_time = explode(' ', $_POST['desired_date_time']);

                $date_selected = $selected_date_time[0];
                $start_time = $date_selected." ".$selected_date_time[1];
                $end_time = $date_selected." ".$selected_date_time[2];


                //Client Details

                $client_first_name = test_input($_POST['client_first_name']);
                $client_last_name = test_input($_POST['client_last_name']);
                $client_phone_number = test_input($_POST['client_phone_number']);
                $client_email = test_input($_POST['client_email']);

                $con->beginTransaction();

                try
                {
					// Check If the client's email already exist in our database
					$stmtCheckClient = $con->prepare("SELECT * FROM clients WHERE client_email = ?");
                    $stmtCheckClient->execute(array($client_email));
					$client_result = $stmtCheckClient->fetch();
					$client_count = $stmtCheckClient->rowCount();

					if($client_count > 0)
					{
						$client_id = $client_result["client_id"];
					}
					else
					{
						$stmtgetCurrentClientID = $con->prepare("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'barbershop' AND TABLE_NAME = 'clients'");
            
						$stmtgetCurrentClientID->execute();
						$client_id = $stmtgetCurrentClientID->fetch();

						$stmtClient = $con->prepare("insert into clients(first_name,last_name,phone_number,client_email) 
									values(?,?,?,?)");
						$stmtClient->execute(array($client_first_name,$client_last_name,$client_phone_number,$client_email));
						
						$client_id = $con->lastInsertId();
					}


                    

                    $stmtgetCurrentAppointmentID = $con->prepare("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'barbershop' AND TABLE_NAME = 'appointments'");
            
                    $stmtgetCurrentAppointmentID->execute();
                    $appointment_id = $stmtgetCurrentAppointmentID->fetch();
                    
                    $stmt_appointment = $con->prepare("insert into appointments(date_created, client_id, employee_id, start_time, end_time_expected ) values(?, ?, ?, ?, ?)");
                    $stmt_appointment->execute(array(Date("Y-m-d H:i"),$client_id[0],$selected_employee,$start_time,$end_time));

                    foreach($selected_services as $service)
                    {
                        $stmt = $con->prepare("insert into services_booked(appointment_id, service_id) values(?, ?)");
                        $stmt->execute(array($appointment_id[0],$service));
                    }
                    
                    echo "<div class = 'alert alert-success'>";
                        echo "Great! Your appointment has been created successfully.";
                    echo "</div>";

                    $con->commit();
                }
                catch(Exception $e)
                {
                    $con->rollBack();
                    echo "<div class = 'alert alert-danger'>"; 
                        echo $e->getMessage();
                    echo "</div>";
                }
            }

        ?>

		<!-- RESERVATION FORM -->

		<form method="post" id="appointment_form" action="appointment.php">
		
			<!-- SELECT SERVICE -->

			<div class="select_services_div tab_reservation" id="services_tab">

				<!-- ALERT MESSAGE -->

				<div class="alert alert-danger" role="alert" style="display: none">
					Please, select at least one service!
				</div>

				<div class="text_header">
					<span>
						1. Choice of services
					</span>
				</div>

				<!-- SERVICES TAB -->
				
				<div class="items_tab">
        			<?php
        				$stmt = $con->prepare("Select * from services");
                    	$stmt->execute();
                    	$rows = $stmt->fetchAll();

                    	foreach($rows as $row)
                    	{
                        	echo "<div class='itemListElement'>";
                            	echo "<div class = 'item_details'>";
                                	echo "<div>";
                                    	echo $row['service_name'];
                                	echo "</div>";
                                	echo "<div class = 'item_select_part'>";
                                		echo "<span class = 'service_duration_field'>";
                                    		echo $row['service_duration']." min";
                                    	echo "</span>";
                                    	echo "<div class = 'service_price_field'>";
    										echo "<span style = 'font-weight: bold;'>";
                                    			echo $row['service_price']."$";
                                    		echo "</span>";
                                    	echo "</div>";
                                    ?>
                                    	<div class="select_item_bttn">
                                    		<div class="btn-group-toggle" data-toggle="buttons">
												<label class="service_label item_label btn btn-secondary">
													<input type="checkbox"  name="selected_services[]" value="<?php echo $row['service_id'] ?>" autocomplete="off">Select
												</label>
											</div>
                                    	</div>
                                    <?php
                                	echo "</div>";
                            	echo "</div>";
                        	echo "</div>";
                    	}
            		?>
    			</div>
			</div>

			<!-- SELECT EMPLOYEE -->

			<div class="select_employee_div tab_reservation" id="employees_tab">

				<!-- ALERT MESSAGE -->

				<div class="alert alert-danger" role="alert" style="display: none">
					Please, select your employee!
				</div>

				<div class="text_header">
					<span>
						2. Choice of employee
					</span>
				</div>

				<!-- EMPLOYEES TAB -->
				
				<div class="btn-group-toggle" data-toggle="buttons">
					<div class="items_tab">
        				<?php
        					$stmt = $con->prepare("Select * from employees");
                    		$stmt->execute();
                    		$rows = $stmt->fetchAll();

                    		foreach($rows as $row)
                    		{
                        		echo "<div class='itemListElement'>";
                            		echo "<div class = 'item_details'>";
                                		echo "<div>";
                                    		echo $row['first_name']." ".$row['last_name'];
                                		echo "</div>";
                                		echo "<div class = 'item_select_part'>";
                                    ?>
                                    		<div class="select_item_bttn">
                                    			<label class="item_label btn btn-secondary active">
													<input type="radio" class="radio_employee_select" name="selected_employee" value="<?php echo $row['employee_id'] ?>">Select
												</label>	
                                    		</div>
                                    <?php
                                		echo "</div>";
                            		echo "</div>";
                        		echo "</div>";
                    		}
            			?>
    				</div>
    			</div>
			</div>


			<!-- SELECT DATE TIME -->

			<div class="select_date_time_div tab_reservation" id="calendar_tab">

				<!-- ALERT MESSAGE -->
				
		        <div class="alert alert-danger" role="alert" style="display: none">
		          Please, select time!
		        </div>

				<div class="text_header">
					<span>
						3. Choice of Date and Time
					</span>
				</div>
				
				<div class="calendar_tab" style="overflow-x: auto;overflow-y: visible;" id="calendar_tab_in">
					<div id="calendar_loading">
						<img src="Design/images/ajax_loader_gif.gif" style="display: block;margin-left: auto;margin-right: auto;">
					</div>
				</div>

			</div>


			<!-- CLIENT DETAILS -->

			<div class="client_details_div tab_reservation" id="client_tab">

                <div class="text_header">
                    <span>
                        4. Client Details
                    </span>
                </div>

                <div>
                    <div class="form-group colum-row row">
                        <div class="col-sm-6">
                            <input type="text" name="client_first_name" id="client_first_name" class="form-control" placeholder="First Name">
							<span class = "invalid-feedback">This field is required</span>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" name="client_last_name" id="client_last_name" class="form-control" placeholder="Last Name">
							<span class = "invalid-feedback">This field is required</span>
                        </div>
                        <div class="col-sm-6">
                            <input type="email" name="client_email" id="client_email" class="form-control" placeholder="E-mail">
							<span class = "invalid-feedback">Invalid E-mail</span>
                        </div>
                        <div class="col-sm-6">
                            <input type="text"  name="client_phone_number" id="client_phone_number" class="form-control" placeholder="Phone number">
							<span class = "invalid-feedback">Invalid phone number</span>
						</div>
                    </div>
        
                </div>
            </div>


			

			<!-- NEXT AND PREVIOUS BUTTONS -->

			<div style="overflow:auto;padding: 30px 0px;">
    			<div style="float:right;">
    				<input type="hidden" name="submit_book_appointment_form">
      				<button type="button" id="prevBtn"  class="next_prev_buttons" style="background-color: #bbbbbb;"  onclick="nextPrev(-1)">Previous</button>
      				<button type="button" id="nextBtn" class="next_prev_buttons" onclick="nextPrev(1)">Next</button>
    			</div>
  			</div>

  			<!-- Circles which indicates the steps of the form: -->

  			<div style="text-align:center;margin-top:40px;">
    			<span class="step"></span>
    			<span class="step"></span>
    			<span class="step"></span>
    			<span class="step"></span>
  			</div>

		</form>
	</div>
</section>



<!-- FOOTER BOTTOM -->

<?php include "Includes/templates/footer.php"; ?>
