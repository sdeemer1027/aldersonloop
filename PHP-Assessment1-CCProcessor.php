<?php
/* Display a form to the user, and process their submission for credit card payment. */
/* If I am to take onwership of this file I would start with contact info and revision ifno in a comment 
***** Conact : myemail@myemail.com
***** Filename : PHP-Assessment1-CCProcessor.php
***** version 1.1
***** Notes of changes : 
***** Need to start session to call session vaiables 
***** The sessions variables are not called,  missing $ on or around lines 125 / 129  
***** to verify, test on sandbox or local wamp 
*/
if (isset($_POST["submit"])) {
	// Get amount.
	$amount = $_POST["amount"];

	// Get card input.
	$cardName = $_POST["cardName"]; // card holder name
	$cardNo = $_POST["cardNo"];
	$cardCode = $_POST["cardCode"];
	$exMo = $_POST["exMo"]; // expiration month
	$exYr = $_POST["exYr"]; // expiration year

	// Get name input.
	$fname = $_POST["fname"]; // first name
	$lname = $_POST["lname"]; // last name
	$company = $_POST["company"];

	// Get address input.
	$addy1 = $_POST["addy1"]; // address, line 1
	$addy2 = $_POST["addy2"]; // line 2
	$city = $_POST["city"];
	$state = $_POST["state"];
	$zip = $_POST["zip"];


	// Submit the CC information.
	require 'cc.php';
	$ccSubmitted = submitCc($amount, $cardName, $cardNo, $cardCode, $exMo, $exYr, $addy1, $addy2, $city, $state, $zip);
	if ($ccSubmitted) {
		// Connect to DB.
		$db = mysql_connect('db.myhost.com', 'root', 'p@ssw0rd');
		if (!$db) {
			die('Could not connect to DB: ' . mysql_error());
		}

		$q1 = mysql_query("update order set timesubmitted = '" . time() . "' where id = " . $_SESSION["orderId"]);
		$q2 = mysql_query("insert into payment (orderid, amount, timecreated, name, ccnumber, cccode, ccexmo, ccexyr, company, line1, line2, city, state, zipcode) values (" . $_SESSION["orderId"] . ", {$amount}, '" . time() . "', '{$cardName}', '{$cardNo}', '{$cardCode}', '{$company}', '{$line1}', '{$line2}', '{$city}', '{$state}', '{$zip}')");

		header("Location: order-complete.php");
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Payment Information</title>
	
	<script src="/js/jquery.min.js"></script>
	<script src="/js/underscore.js"></script>
	<script src="/scripts/angular.js"></script>
	<script src="/js/jquery-overlay.js"></script>
	<script src="/js/jquery.ui.js"></script>
	<script src="/scripts/common.js"></script>
	<script src="/scripts/messaging.js"></script>

	<style type="text/css">
		.instructions {
			font-style: italic;
		}

		table td {
			padding-right: 10px;
		}

		.required {
			font-weight: bold;
		}
	</style>
</head>
<body>
	<?php require 'header.php'; ?>

	<h1>Payment Information</h1>
	<div class="instructions">Please, fill out the following form to submit your payment.</div>

	<script>
		$('table td:last').css('padding-right', 0);
	</script>

	<form method="post">
		<input type="hidden" name="amount" value='<?php echo $_SESSION["orderAmount"]; ?>' />

		<div id="section" style="border: 1px solid #0993; margin-bottom: 15px; padding: 7px;">
			<h3>Credit Card Information</h3>
			
			<div class="label required">Cardholder Name</div>
			<input type="text" name="cardName" />

			<table cellspacing="0">
				<tr>
					<td>
						<div class="label" style="font-weight: bold;">Card Number:</div>
						<input type="text" name="cardNo" required="required" />
					</td>
					<td>
						<div class="label" style="font-weight: bold;">Card Code</div>
						<input type="text" name="cardCode" required="required" />
					</td>
					<td>
						<div class="label" style="font-weight: bold;">Expiration:</div>
						<input type="text" name="exMo" required="required" /> / <input type="text" name="exYr" required="required" />
					</td>
				</tr>
			</table>
		</div>

		<div id="section" style="border: 1px solid #066; margin-bottom: 16px; padding: 10px;">
			<h3>Billing Information</h3>
			
			<table cellspacing="0">
				<tr>
					<td>
						<div class="label required">First Name:</div>
						<input type="text" name="fname" value='<?php echo _SESSION["firstName"]; ?>' required />
					</td>
					<td style="padding-right: 0">
						<div class="label required">Last Name</div>
						<input type="text" name="lname" value='<?php echo _SESSION["lastName"]; ?>' required />
					</td>
				</tr>
			</table>

			<div class="label">Company</div>
			<input type="text" name="company" />

			<div class="label" style="font-weight: bold;">Address</div>
			<input type="text" name="addy1" required="required" />
			<input type="text" name="addy2" />

			<table cellspacing="0">
				<tr>
					<td>
						<input type="text" name="city" />
					</td>
					<td>
						<input type="text" name="state" />
					</td>
					<td>
						<input type="text" name="zip" />
					</td>
				</tr>
			</table>
		</div>

		<input type="submit" name="submit" value="Submit Payment" />
	</form>

	<?php require 'footer.php'; ?>
</body>
</html>
