<?php
/**
Allows the user to reset their password
 */
error_reporting(E_COMPILE_ERROR);
session_start();
include("database.php");
include("Email.php");//Required for sending emails
?>
<title>Reset Password</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js">
    //Uses the 'zxcbn' library by Dropbox which calculates the strength of the password and returns corresponding strength level
</script>
<script>
    function validate() //Checks if Password and Confirm Password matches each other
    {
        var a = document.getElementById("txtPassword").value;
        var b = document.getElementById("txtConpass").value;
        var c = document.getElementById("CName").value;
        regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$/; //Regex to check if password matches the required parameters

        //Checks if passwords fulfil all parameters
        if (a!==b)
        {
            alert("Passwords do not match");
            return false;
        }
        else if(!regex.test(a) || a.length < 8) //Test password; confirm password test is redundant as it needs to match password
        {
            alert("Error: Password must contain at least 8 characters, 1 uppercase, 1 lowercase, 1 number and 1 special character!");
            a.focus();
            return false;
        }
        else if(a === c) //Checks if password matches username
        {
            alert("Password cannot be the same as username!");
            return false;
        }
        else //Password fulfils all requirements
        {
            return true;
        }
    }

    function callfunction(source) //Checks if password matches the required parameters
    {
        var textBox = source;
        var textLength = textBox.value.length;
        regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$/; //Regex to check if password matches the required parameters
        if(!regex.test(textBox.value) || textLength<8)
        {
            textBox.title = "Password must be at least 8 characters long, contain 1 uppercase, 1 lowercase, 1 number and 1 special character ";
            textBox.style.borderColor = "red";
        }
        else textBox.style.borderColor = "green";
    }

    function testStrength(source) //Checks the password strength
    {
        var meters = document.getElementById('password-strength-meter');
        var texts = document.getElementById('password-strength-text');

        var val = source.value;
        var result = zxcvbn(val); //Calls the zxcvbn function to check password strength

        // Update the password strength meter
        meters.value = result.score;


        // Update the text indicator
        if(val !== "")
        {
            texts.innerHTML = "Strength: " + "<strong>" + strength[result.score] + "</strong>" + "<span class='feedback'>"
                + result.feedback.warning + " " + result.feedback.suggestions + "</span>";
        }
        else
        {
            texts.innerHTML = "";
        }
    }

    var strength =  //Outputs messages based on password strength
    {
        0: "Very Weak",
        1: "Weak",
        2: "Medium",
        3: "Strong",
        4: "Strong"
    }
</script>
<body>
<div class="container" style="width: 80%">
    <h3>*Mandatory</h3>
    <form method="post" action="" onsubmit="return validate()">
        <?php
        $Name = $_GET['Name'];
        $ID = $_GET['Id'];
        //$stmt used to ensure SQL injection prevention via parameterized query
        $stmt = $Link->prepare('SELECT fullName FROM tbllogin WHERE fullName = ? AND userId = ?'); //Checks if the name and id are from the same account to ensure password change does not occur to other accounts
        //Bind parameters
        $stmt->bind_param("ss", $Name, $ID);
        //Execute query
        $stmt->execute();
        //Bind results
        $stmt->bind_result($Result);
        $stmt->store_result(); //Stores the results for num_rows to inspect

        if($stmt->num_rows > 0)
        {
            while ($stmt->fetch())
            {
                ?>
                <table>
                    <caption>Reset Password</caption>
                    <tr>
                        <td style="width: 50%"><label for="CName">Name: </label></td>
                        <td><input type="text" name="CName" id="CName" size="52"
                                   value="<?php echo $Result; ?>" style="background-color: lightgray"
                                   readonly></td>
                    </tr>
                    <tr>
                        <td colspan="2"><h2 style="color: darkred">Enter your new password</h2></td>
                    </tr>
                    <tr>
                        <td style="text-align: center" colspan="2">Passwords must be at least 8 characters long, contain 1 uppercase, 1 lowercase, 1 number and 1 special character</td>
                    </tr>
                    <tr>
                        <td align="center" ><label for="txtPassword">*Password: </label></td>
                            <td><input name="txtPassword" id="txtPassword" type="password" value="" oninput="testStrength(this)" onblur="callfunction(this)" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$" required/></td>
                    </tr>
                    <!-- pattern attribute is used to set the regex for the password -->
                    <tr>
                        <td></td><td><meter max="4" min="0" value="0" id="password-strength-meter"></meter> <br/>
                            <label id="password-strength-text"></label></td>
                    </tr>
                    <!-- Meter and label is used to show password strength and possible suggestions for improvement -->
                    <tr>
                        <td align="center"><label for="txtConpass">*Reconfirm Password: </label></td>
                            <td><input name="txtConpass" id="txtConpass" type="password" value="" onblur="callfunction(this)" required/></td>
                    </tr>
                    <!-- No pattern is used for reconfirm password as it has to match password, making pattern redundant-->
                </table>
                <br/>
                <input type="submit" name="btnSubmit" value="Change Password" class="button"
                       onclick='return confirm("Your password will be changed. Proceed?");'>
                <?php
            }
        }
        ?>
    </form>
</div>
</body>
<?php
if($_REQUEST['btnSubmit'])
{
    if($_POST['txtPassword'] == "" || $_POST['txtConpass'] == "") echo "<script>alert('Please fill in all password fields')</script>";
    else if($_POST['txtPassword'] == $_POST['CName']) echo "<script>alert('Password and username cannot be the same!')</script>";
    else
    {
        $newPass = sha1($_POST['txtPassword']);
        $Name = $_POST['CName'];
        $ID = $_GET['Id'];

        $stmt = $Link->prepare('SELECT userId FROM tblformpass WHERE userId = ? AND formPass = ?'); //Checks if the verification code is correct and for an unactivated account
        //Bind parameters
        $stmt->bind_param("ss", $ID, $newPass);
        //Execute query
        $stmt->execute();
        //Bind results
        $stmt->bind_result($Result);
        $stmt->store_result(); //Stores the results for num_rows to inspect

        if($stmt->num_rows > 0)
        {
            $stmt ->close();

            echo "<script>alert('You have previously used this password already, please choose a new one');</script>";
            $URL = "changePass.php?Name=" . $Name . "&Id=" . $ID;
            echo "<script>location.href = '$URL';</script>"; //Reloads the page
        }
        else
        {
            $stmt = $Link->prepare('INSERT INTO tblformpass(userId, formPass) VALUES (?,?)'); //Inserts password as a former password to avoid changes
            $stmt->bind_param("ss", $ID, $newPass);
            $stmt->execute();
            
            $stmt= $Link->prepare('UPDATE tbllogin SET Password=? WHERE fullName=? AND userId=?'); //Updates account password
            //Bind parameters
            $stmt->bind_param("sss", $newPass,$Name, $ID);
            //Execute query
            $stmt->execute();

            $stmt ->close();

            echo "<script>alert('Password change succeeded');</script>";
            echo "<script>location.replace('Login.php');</script>";
        }


    }
}
?>
