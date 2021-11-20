<?php
/** Creates the database of the system and a default administrator account */
//Database used: MySQL on XAMPP v3.2.2
error_reporting();//Cancels notifications of errors and warnings to prevent showing up on webpage
$Username = "username";
$Pass = "password";
$host = "localhost";
$Name = "dbsecurity"; //Database name
$TableList = array( //Creates an array storing database records
    "CREATE TABLE tbllogin(
		userId int PRIMARY KEY AUTO_INCREMENT,
		fullName VARCHAR(100),
		Password CHAR(40), 
		veriCode CHAR(32),
		Email VARCHAR(100),
		Status char(1) DEFAULT 'N'
	)",

    "CREATE TABLE tblformpass(
        fPassId int PRIMARY KEY AUTO_INCREMENT,
        userId int,
        formPass CHAR(40) 
)");
//2 tables
//Password is stored in database as SHA1 hash, which is a set 40 characters long
//Status is used to determine if the account is activated, deactivated or inactive
$Link = mysqli_connect($host, $Username, $Pass, $Name) or die("The site is unable to connect to the database, please contact the team's administrator");

if($Link) //Connected to database
{
    if(!mysqli_select_db($Link, $Name)) //Database does not exist
    {
        $SQL = "CREATE DATABASE ". $Name;
        mysqli_query($Link,$SQL);
    }
    mysqli_select_db($Link, $Name);
    for($i = 0; $i<count($TableList);++$i)
    {
        mysqli_query($Link, $TableList[$i]); //Checks if all tables are in the database and insert any missing tables
    }
    $SQL = "SELECT * FROM tbllogin WHERE fullName = 'ADMIN' AND Password = '".sha1("ayuwoki990")."' AND Status = 'A'"; //Checks if default admin account is in the database
    $Result = mysqli_query($Link, $SQL);
    if(mysqli_num_rows($Result) == 0)
    {
        $SQL = "INSERT INTO tbllogin(fullName, Password, Email, Status) VALUES('ADMIN','".sha1('ayuwoki990')."','','A')";
        $Result = mysqli_query($Link, $SQL); //Inserts a new admin account(for easier system testing)
    }

}
else //Failed to connect
{
    echo "<script language='JavaScript'>alert('Failed to connect');</script>";
}
?>