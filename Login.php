<?php
/** Allows users to login or head to registration*/
error_reporting(1);
session_start();
include("database.php");
?>
<title>Login</title>
<body>
<?php
if($_REQUEST['btnLogin'])
{
    if($_POST['txtUsername'] == "" || $_POST['txtPassword'] == "")
    {
        echo "<script>alert('You have not entered a username or password!')</script>";
        echo "<script>location='Login.php'</script>";
    }
    else
    {
        $Name = $_POST['txtUsername'];
        $Pass = sha1($_POST['txtPassword']);
        $Status = "A";

        //$stmt used to ensure SQL injection prevention via parameterized query
        $stmt = $Link->prepare('SELECT userId FROM tbllogin WHERE fullName = ? AND Password = ? AND Status = ?'); //Checks if the account exists and is activated
        //Bind parameters
        $stmt->bind_param("sss", $Name,$Pass,$Status);
        //Execute query
        $stmt->execute();
        $stmt->store_result(); //Stores the results for num_rows to inspect
        //Bind results
        $stmt->bind_result($Result);

        if ($stmt->num_rows > 0) //Account exists and is activated
        {
            while ($stmt->fetch())
            {
                echo "<script>alert('Credentials accepted');</script>";
                $URL = "changePass.php?Name=" . $Name . "&Id=" . $Result;
                echo "<script>location.href = '$URL';</script>";// Goes to the change password page(No home page as it is not required for this work)
            }
        }
        else
        {
            $Status = "N";

            //$stmt used to ensure SQL injection prevention via parameterized query
            $stmt = $Link->prepare('SELECT fullName FROM tbllogin WHERE fullName = ? AND Password = ? AND Status = ?'); //Checks if the account exists but is not activated
            //Bind parameters
            $stmt->bind_param("sss", $Name,$Pass,$Status);
            //Execute query
            $stmt->execute();
            //Bind results
            $stmt->bind_result($ResultN);
            $stmt->store_result(); //Stores the results for num_rows to inspect

            if ($stmt->num_rows > 0) //Account exists but not activated
            {
                echo "<script>alert('You have not yet activated your account using the code sent to your email, please do so now');</script>";
                echo "<script>location = 'verifyAccount.php';</script>";
            }
            else //Account does not exist with inputted username/password
            {
                echo "<script>alert('Invalid Username or Password'); </script>";
                echo "<script>location = 'Login.php'; </script>";
            }
        }
    }
}
?>
<link rel="stylesheet" href="Default%20Theme.css">
<div class="container" style="width: 35%; height: 85%">
    <form id="form1" name="form1" method="post" action="">
        <div align="center"><table width="30%" border="0">
                <caption>Log In</caption>
                <tr>
                </tr>
                <tr style="background-color: inherit">
                    <td align="center"><label for="txtUsername">Name: </label><input name="txtUsername" id="txtUsername" type="text" value="" required/></td>
                </tr>
                <tr>
                    <td align="center"><label for="txtPassword">Password: </label><input name="txtPassword" id="txtPassword" type="password" value="" required/></td>
                </tr>
                <tr style="background-color: inherit">
                    <td><br/><div align="center">
                            <input name="btnLogin" class = "button"  type="submit"  id="btnLogin" value="Log in" /></div></td>
                </tr>
            </table>
        </div>
    </form>
</div>
</body>