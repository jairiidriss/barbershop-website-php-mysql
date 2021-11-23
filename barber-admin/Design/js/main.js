

/* ============ TITLE TOOLTIP TOOGLE ============== */
	
$(function () 
{
	$('[data-toggle="tooltip"]').tooltip();
});


/*
	============================

	VALIDATE LOGIN FORM
	
	============================
*/

function validateLogInForm() 
{
	var username_input = document.forms["login-form"]["username"].value;
	var password_input = document.forms["login-form"]["password"].value;

	if (username_input == "" && password_input == "") 
    {
    	document.getElementById('required_username').style.display = 'initial';
    	document.getElementById('required_password').style.display = 'initial';
    	return false;
    }
    
    if (username_input == "") 
   	{
    	document.getElementById('required_username').style.display = 'initial';
    	return false;
    }
    if(password_input == "")
    {
    	document.getElementById('required_password').style.display = 'initial';
        return false;
    }
}


/*
    ======================================
    
    DASHBOARD PAGE ==== > TOGGLE BOOKINGS TABS IN DASHBOARD PAGE

    ========================================
*/

function openTab(evt, tabName) 
{
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    
    for (i = 0; i < tabcontent.length; i++) 
    {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablinks");

    for (i = 0; i < tablinks.length; i++) 
    {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    
    document.getElementById(tabName).style.display = "table";
    evt.currentTarget.className += " active";
}

/*
    ======================================
    
    DASHBOARD PAGE ==== > CANCEL APPOINTMENT WHEN CANCEL BUTTON IS CLICKED

    ========================================
*/

$('.cancel_appointment_button').click(function()
{

    var appointment_id = $(this).data('id');
    var cancellation_reason = $('#appointment_cancellation_reason_'+appointment_id).val();
    var do_ = 'Cancel Appointment';


    $.ajax({
        url: "ajax_files/appointments_ajax.php",
        type: "POST",
        data:{do:do_,appointment_id:appointment_id,cancellation_reason:cancellation_reason},
        success: function (data) 
        {
            //Hide Modal
            $('#cancel_appointment_'+appointment_id).modal('hide');
            
            //Show Success Message
            swal("Cancel Appointment","The Appointment has been canceled successfully!", "success").then((value) => 
            {
                window.location.replace("index.php");
            });
            
        },
        error: function(xhr, status, error) 
        {
            alert('ERROR HAS BEEN OCCURRED WHILE TRYING TO PROCESS YOUR REQUEST!');
        }
      });
});


/*
    ======================================
    
    SERVICE CATEGORIES PAGE ==== > ADD SERVICE CATEGORY BUTTON IS CLICKED

    ========================================
*/


$('#add_category_bttn').click(function()
{
    var category_name = $("#category_name_input").val();
    var do_ = "Add";

    if($.trim(category_name) == "")
    {
        $('#required_category_name').css('display','block');
    }
    else
    {
        $.ajax(
        {
            url:"ajax_files/service_categories_ajax.php",
            method:"POST",
            data:{category_name:category_name,do:do_},
            dataType:"JSON",
            success: function (data) 
            {
                if(data['alert'] == "Warning")
                {
                    swal("Warning",data['message'], "warning").then((value) => {});
                }
                if(data['alert'] == "Success")
                {
                    $('#add_new_category').modal('hide');
                    swal("New Category",data['message'], "success").then((value) => 
                    {
                        window.location.replace("service-categories.php");
                    });
                }
                
            },
            error: function(xhr, status, error) 
            {
                alert('AN ERROR HAS BEEN ENCOUNTERED WHILE TRYING TO EXECUTE YOUR REQUEST');
            }
        });
    }
});


/*
    ======================================
    
    SERVICE CATEGORIES PAGE ==== > ADD SERVICE CATEGORY BUTTON IS CLICKED

    ========================================
*/



$('.delete_category_bttn').click(function()
{
    var category_id = $(this).data('id');
    var action = "Delete";

    $.ajax(
    {
        url:"ajax_files/service_categories_ajax.php",
        method:"POST",
        data:{category_id:category_id,action:action},
        dataType:"JSON",
        success: function (data) 
        {
            if(data['alert'] == "Warning")
                {
                    swal("Warning",data['message'], "warning").then((value) => {});
                }
                if(data['alert'] == "Success")
                {
                    swal("New Category",data['message'], "success").then((value) => 
                    {
                        window.location.replace("service-categories.php");
                    });
                }     
        },
        error: function(xhr, status, error) 
        {
            alert('AN ERROR HAS BEEN ENCOUNTERED WHILE TRYING TO EXECUTE YOUR REQUEST');
            alert(error);
        }
      });
});


$('.edit_category_bttn').click(function()
{
    var category_id = $(this).data('id');
    var category_name = $("#input_category_name_"+category_id).val();

    var action = "Edit";

    if($.trim(category_name) == "")
    {
        $('#invalid_input_'+category_id).css('display','block');
    }
    else
    {
        $.ajax(
        {
            url:"ajax_files/service_categories_ajax.php",
            method:"POST",
            data:{category_id:category_id,category_name:category_name,action:action},
            dataType:"JSON",
            success: function (data) 
            {
                if(data['alert'] == "Warning")
                {
                    swal("Warning",data['message'], "warning").then((value) => {});
                }
                if(data['alert'] == "Success")
                {
                    swal("New Category",data['message'], "success").then((value) => 
                    {
                        window.location.replace("service-categories.php");
                    });
                }     
            },
            error: function(xhr, status, error) 
            {
                alert('AN ERROR HAS BEEN ENCOUNTERED WHILE TRYING TO EXECUTE YOUR REQUEST');
                alert(error);
            }
        });
    }
});


/*
    ======================================
    
    SERVICEs PAGE ==== > DELETE SERVICE BUTTON IS CLICKED

    ========================================
*/


$('.delete_service_bttn').click(function()
{
    var service_id = $(this).data('id');
    var do_ = "Delete";

    $.ajax(
    {
        url:"ajax_files/services_ajax.php",
        method:"POST",
        data:{service_id:service_id,do:do_},
        success: function (data) 
        {
            swal("Delete Service","The service has been deleted successfully!", "success").then((value) => {
                window.location.replace("services.php");
            });     
        },
        error: function(xhr, status, error) 
        {
            alert('AN ERROR HAS BEEN ENCOUNTERED WHILE TRYING TO EXECUTE YOUR REQUEST');
        }
      });
});


/*
    ======================================
    
    EMPLOYEES SCHEDULE PAGE ==== >  SHOW DAY FROM TO HOURS BUTTON IS CLICKED

    ========================================
*/


$(".sb-worktime-day-switch").click(function()
{
    if(!$(this).prop('checked'))
    {
        $(this).closest('div.worktime-day').children(".time_").css('display','none');
    }
    else
        $(this).closest('div.worktime-day').children(".time_").css('display','flex');
});


/*
    ======================================
    
    EMPLOYEES PAGE ==== > DELETE EMPLOYEE BUTTON IS CLICKED

    ========================================
*/


 $('.delete_employee_bttn').click(function()
{
    var employee_id = $(this).data('id');
    var do_ = "Delete";

    $.ajax(
    {
        url:"ajax_files/employees_ajax.php",
        method:"POST",
        data:{employee_id:employee_id,do:do_},
        success: function (data) 
        {
            swal("Delete Employee","The employee has been deleted successfully!", "success").then((value) => {
                window.location.replace("employees.php");
            });     
        },
        error: function(xhr, status, error) 
        {
            alert('AN ERROR HAS BEEN ENCOUNTERED WHILE TRYING TO EXECUTE YOUR REQUEST');
        }
    });
});