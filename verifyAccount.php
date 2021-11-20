<?php
/**
Verifies that the account belongs to the correct user
 */
error_reporting(E_COMPILE_ERROR);
session_start();
include("database.php");
?>
<title>Verify Account</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container" style="width: 80%">
    <h1>Verify Account</h1>
    <h4>A verification code has been sent to your email, please check your email now</h4>
    <h3>Enter the verification code you have received in your email</h3>
    <br/>
    <form method="post">
        <table>
            <caption>Enter Code</caption>
            <tr>
                <td style="width: 40%; text-align: right"><label for="vCode">Verification Code: </label></td>
                <td><input type="text" name="vCode" id="vCode" maxlength="32" size="34" required> </td>
            </tr>
            <tr>
                <td colspan="100%"><input type="submit" name="btnSub" id="btnSub" value="Submit Code" class="button"> </td>
            </tr>
        </table>
    </form>
    <br/>
    <?php
    if($_REQUEST['btnSub'])
    {
        $code = trim($_POST['vCode']);
        $status = 'N';

        $stmt = $Link->prepare('SELECT Status FROM tbllogin WHERE Status = ? AND veriCode = ?'); //Checks if the verification code is correct and for an unactivated account
        //Bind parameters
        $stmt->bind_param("ss", $status, $code);
        //Execute query
        $stmt->execute();
        //Bind results
        $stmt->bind_result($Result);
        $stmt->store_result(); //Stores the results for num_rows to inspect

        if($stmt->num_rows > 0)
        {

            $status = 'A';

            $stmt= $Link->prepare('UPDATE tbllogin SET Status=? WHERE veriCode=?'); //Sets account to active
            //Bind parameters
            $stmt->bind_param("ss", $status,$code);
            //Execute query
            $stmt->execute();

            echo "<h1 style='color: green;'>Your Account Has Been Successfully Verified!</h1>";
            echo "<br/><br/>";
            echo "<a href='Login.php'><input type='button' value='Proceed to Login' class='button' style=\" font-size: 36px\"></a>"; //Sends visitor to the login page
        }
        else
        {
            echo "<h1 style='color: red;'>You have either entered the wrong code, or your account has already been verified</h1>";
        }
        //Close statement
        $stmt->close();
    }
    ?>
</div>
</body>
