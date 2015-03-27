<?php

    //No script on this page will run until the connection script has been included within the page
    require "inc/conx.php";

    //Start a new session so that the user remains logged in and the user's login data is remembered whilst on the site
    session_start();

    //Check whether the session has been set using the user ID
    if (!isset($_SESSION['id'])) {
        //If the session has not been set(user is not logged in), include the login tools script
        require 'inc/login_tools.php';
        //Use the load function within the login tools script
        //This will redirect the user back to the index page where they will need to login 
        load();
    }

    //Check to see if the date has been date within the navigation bar
    if ($_GET['date']) {
        //If yes, assign the date to a variable
    	$getDate = $_GET['date'];

        //Get all results from the wastage table which have the date passed as a GET request
    	$resultGet = mysqli_query($dbc, "SELECT * FROM wastage WHERE date = '$getDate'");

        //Reformat the GET date
    	$newGetDate = date("l jS F Y", strtotime($getDate));

    	?>
        
    		<!doctype html>
    		<html lang="en">
    			<head>
    				<link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
    				<title>Notcutts Web System</title>
    			</head>
    			<body>
    				
    				<div class="container">
    					<div class="jumbotron" style="margin-top: 20px;">
    						<legend>Results For: <b><?php echo $newGetDate; ?></b></legend>
    						<table class="table table-bordered">
    							<thead>
    								<tr>
    									<th>ID</th>
    									<th>Item</th>
    									<th>Amount Wasted</th>
    								</tr>
    							</thead>
    							<tbody>
    								<?php

                                        //Fetch all objects using the query created earlier...
                                        //Output all items within a table
    									while ($rowGet = mysqli_fetch_object($resultGet)) {
    										echo '
    											<tr>
    												<td>' . $rowGet->id . '</td>
    												<td>' . $rowGet->item . '</td>
    												<td>' . $rowGet->amountWasted . '</td>
    											</tr>
    										';
    									}

    								?>
    							</tbody>
    						</table>
    						<a href="wastage.php" class="btn btn-danger">Back</a>
    					</div>
    				</div>

    				<!-- Javascript Includes -->
			        <script src="js/jquery.js"></script>
			        <script src="js/bootstrap.js"></script>
                    <script src="flot/jquery.flot.js"></script>
                    <script src="flot/axislabel.js"></script>

    			</body>
    		</html>

<?php

    } else {

?>

<!doctype html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
		<title>Notcutts Web System</title>
	</head>
	<body>

		<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <a href="home.php" class="navbar-brand">Notcutts Web System</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li><p class="navbar-text"><span class="glyphicon glyphicon-user"></span> Logged In As: <b><?php echo ucfirst($_SESSION['name']); ?></b></p></li>
                    <li class="active"><a href="home.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-off"></span> Logout</a></li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <ol class="breadcrumb">
                <li><a href="home.php">Home</a></li>
                <li>Wastage Sheets</li>
            </ol>
        </div>

        <?php

        	

        ?>

        <div class="container">
        	<div class="jumbotron">
        		<button class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                    Add New Sheet
                </button>  
        		<br />
        		<br />
        		<table class="table">
        			<thead>
        				<tr>
        					<th><p>Wastage Sheets - <b>(Newest First)</b></p></th>
        				</tr>
        			</thead>
        			<tbody>
        				<?php

                            //Select a single date from the wastage table and order them in descending order
	        				$result = mysqli_query($dbc, "SELECT DISTINCT date FROM wastage ORDER BY date DESC");

                            //Get the amount of rows that match the query
				        	$checkNum = mysqli_num_rows($result);

                            //Check to see if there are more than 0 items that match the query
				        	if ($checkNum !== 0) {
                                //Gather all data using the query and turn all returned items in to objects
					        	while ($row = mysqli_fetch_object($result)) {
                                    //Assign the id from the table to a variable
					        		$id = $row->id;
                                    //Assign the date from the table to a variable
					        		$date = $row->date;

                                    //Reformat the date from the table
					        		$newDate = date("l jS F Y", strtotime($date));

					        		echo '
					        		<tr>
					        			<td>
					        				<a href=wastage.php?date=' . $date . ' class="btn btn-primary form-control">' . $newDate  . '</a>
					        			</td>
					        		</tr>
					        		';
					        	}
					        } else {
					        	echo '
					        	<tr>
					        		<td class="danger">
					        			No Items Found
					        		</td>
					        	</tr>
					        	';
					        }

			        	?>
        			</tbody>
        		</table>
        	</div>
        </div>

        <?php

            //Check that the form submit button has been pressed
        	if (isset($_POST['wasteSubmit'])) {
                //Chedk to make sure the name input field is not empty
        		if (!empty($_POST['wasteName'])) {
                    //Check to make sure the amount input field is not empty
        			if (!empty($_POST['wasteAmount'])) {
                        //Check to see if the price input field is not empty
                        if (!empty($_POST['wastePrice'])) {
                            //Assign the value of the name input field to a variable
            				$wasteName = $_POST['wasteName'];
                            //Assign the value of the amount input field to a variable
            				$wasteAmount = $_POST['wasteAmount'];
                            //Assign the value of the price input field to a variable
                            $wastePrice = $_POST['wastePrice'];

                            //Run a query to insert the values of all of the input fields into the wastage table
            				mysqli_query($dbc, "INSERT INTO wastage (item, amountWasted, itemPrice, `date`) VALUES('$wasteName', '$wasteAmount', '$wastePrice', now())");
            				//Redirect the user back to wastage.php
                            header('location: wastage.php');
                        } else {
                            //Output an error to the user
                            echo '<div class="alert alert-danger">Please Fill In All Fields</div>';
                        }
        			} else {
                        //Output an error to the user
        				echo '<div class="alert alert-danger">Please Fill In All Fields</div>';
        			}
        		} else {
                    //Output an error to the user
        			echo '<div class="alert alert-danger">Please Fill In All Fields</div>';
        		}
        	}

        ?>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Item To Sheet</h4>
              </div>
              <div class="modal-body">
                <table class="table">
                    <tbody>
                        <form role="form" method="POST">
                            <tr>
                                <td><input type="text" class="form-control" name="wasteName" placeholder="Wasted Item Name..." /></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" name="wasteAmount" placeholder="Amount Wasted..." /></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" name="wastePrice" placeholder="Item Price..." /></td>
                            </tr>
                            <tr>
                                <td><input type="submit" class="form-control btn btn-success" name="wasteSubmit" value="Add To Sheet" /></td>
                            </tr>
                        </form>
                    </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
		
		<!-- Javascript Includes -->
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.js"></script>

	</body>
</html>

<?php

	}

?>