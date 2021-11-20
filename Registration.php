<?php
/**Allows a visitor to register on the system with full name, email and password*/
error_reporting(1);//Cancels notifications of errors and warnings to prevent showing up on webpage
include("database.php");//database.php is called to ensure database integrity
include("Email.php");//Required for sending emails
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js">
    //Uses the 'zxcbn' library by Dropbox which calculates the strength of the password and returns corresponding strength level
</script>
<script>
    function validate() //Checks if Password matches Confirm Password, and if Password and Email are formatted correctly
    {
        var a = document.getElementById("txtPassword").value;
        var b = document.getElementById("txtConpass").value;
        var c = document.getElementById("txtName").value;
        var d = document.getElementById("txtEmail").value;
        regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$/; //Regex to check if password matches the required parameters
        regEm = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/; //Regex to check if email address entered is valid

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
        else if(!regEm.test(d)) //Checks if email format is correct(Backup test, not necessary due to HTML5 dealing with this)
        {
            alert("You have entered an inavlid email address, please recheck");
            d.focus();
            return false;
        }
        else //Passwords and email are correctly formatted
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
<?php
if($_REQUEST['btnReg'])
{
    //All validation done via javascript, redundant to redo on php as wel
    $name = trim($_POST['txtName']);
    $email = trim($_POST['txtEmail']);
    $password = sha1($_POST['txtPassword']); //sha1 will be used to hash the password
    $getpass = $_POST['txtPassword'];
    $conpass = $_POST['txtConpass'];

    if($name == "" || $email == "" || $getpass = "" || $conpass = "") //Checks if all fields have been entered
    {
        echo "<script>alert('You have not filled in all the fields!');</script>";
    }
    else
    {
        // $stmt used to ensure SQL injection prevention via parameterized query
        $stmt = $Link->prepare('SELECT userId FROM tbllogin WHERE Email=?'); //Checks if the email address has been used before
        //Bind parameters
        $stmt->bind_param("s", $email);
        //Execute query
        $stmt->execute();
        //Bind results
        $stmt->bind_result($Result);
        $stmt->store_result(); //Stores the results for num_rows to inspect

        if($stmt->num_rows > 0)
        {
            echo "<script>alert('This email is already registered here!');</script>";
        }
        else
        {
            // generate a 16 byte random hex string(For multifactor authentication)
            $random_hash = bin2hex(openssl_random_pseudo_bytes(16));
            $gethash = $random_hash;

            //Insert record into database
            $stmt = $Link->prepare('INSERT INTO tbllogin(fullName, Password, veriCode, Email) VALUES (?,?,?,?)'); //Insert registered record
            //Bind parameters
            $stmt->bind_param("ssss", $name,$password,$gethash,$email);

            //Execute query
            $stmt->execute();

            $stmt = $Link->prepare('SELECT userId FROM tbllogin WHERE Email=?');
            //Bind parameters
            $stmt->bind_param("s", $email);
            //Execute query
            $stmt->execute();
            //Bind results
            $stmt->bind_result($Result);
            $stmt->store_result(); //Stores the results for num_rows to inspect
            if($stmt->num_rows > 0)
            {
                while($stmt->fetch())
                {
                    $stmt = $Link->prepare('INSERT INTO tblformpass(userId, formPass) VALUES (?,?)'); //Inserts password as a former password to avoid changes
                    $stmt->bind_param("ss", $Result, $password);
                    $stmt->execute();
                }
                echo "<script>alert('Your registration has been accepted!');</script>";
            }
            else echo "<script>alert('Sorry, an error has occured, please try again or contact an adminstrator!');</script>";
            //Close statement
            $stmt->close();

            //Email body
            $Verify = "To complete your registration on the Cybersecurity website, please enter the code below to verify your account<br/>
                                    ".$gethash." <br/> Please ignore if you did not create an account";
            sendEmail("Account Verification", $Verify, $email); //Sends an email to the registerer with the verification code

            echo "<script>location='verifyAccount.php';</script>";
        }
    }
}
?>
<title>Registration</title>
<body>
<link rel="stylesheet" href="Default%20Theme.css">
<div class="container" style="width: 80%" >
    <form id="register" name="register" method="post" action="" onsubmit=" return validate()">
        <div align="center">
            <h3>*Mandatory</h3>
            <table width="30%" border="0">
                <caption>Register</caption>
                <tr>
                </tr>
                <tr style="background-color: inherit">
                    <td align="center" width="50%"><label for="txtName">*Full Name: </label></td>
                        <td><input name="txtName" id="txtName" type="text" value="" maxlength="100" size="50" required/></td>
                </tr>
                <tr style="background-color: inherit">
                    <td align="center"><label for="txtEmail">*Email: </label></td>
                        <td><input type="email" name="txtEmail" id="txtEmail" value="" maxlength="100" size="50" required/> </td>
                </tr>
                <!-- HTML5 input type "email" automatically checks email validation -->
                <!--  Inadvisable to use pattern in conjunction due to possible conflicts with input type "email" -->
                <tr>
                    <td style="text-align: center" colspan="2">Passwords must be at least 8 characters long, contain 1 uppercase, 1 lowercase, 1 number and 1 special character</td>
                </tr>
                <tr>
                    <td align="center"><label for="txtPassword">*Password: </label></td>
                        <td><input name="txtPassword" id="txtPassword" type="password" value="" oninput="testStrength(this)" onblur="callfunction(this)" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$" required/></td>
                </tr>
                <!-- pattern attribute is used to set the regex for the password -->
                <tr>
                    <td></td><td><meter max="4" min="0" value="0" id="password-strength-meter" style="width: 45%;"></meter> <br/>
                        <label id="password-strength-text"></label></td>
                </tr>
                <!-- Meter and label is used to show password strength and possible suggestions for improvement -->
                <tr>
                    <td align="center"><label for="txtConpass">*Reconfirm Password: </label></td>
                        <td><input name="txtConpass" id="txtConpass" type="password" value="" onblur="callfunction(this)" required/></td>
                </tr>
                <!-- No pattern is used for reconfirm password as it has to match password, making pattern redundant-->
                <tr style="background-color: inherit">
                    <td colspan="2"><br/><div align="center">
                            <input name="btnReg" class = "button"  type="submit"  id="btnReg" value="Register" onclick='return confirm("Your registration will proceed shortly. Continue?");' /></div></td>
                </tr>
            </table>
        </div>
    </form>
</div>
</body>


