<?php
$dbhostname = "localhost";
$dbusername = "mitate";
$dbpassword = "Database4Mitate";
$dbschemaname = "mitate";
$dbconnection = mysql_connect($dbhostname, $dbusername, $dbpassword);
if (!$dbconnection)	{die('Could not connect: ' . mysql_error());}
mysql_select_db($dbschemaname, $dbconnection);
$loginresultset = mysql_query("SELECT count(*) as status FROM userinfo where username = '$_POST[username]' and password = '$_POST[password]'");
if ($loginresultset) {
    $loginresultrow = mysql_fetch_assoc($loginresultset);
    if ($loginresultrow['status'] == "1") {
		echo "Logged in. Please wait...\n";
		$get_user_list = mysql_query("select * from userinfo where username = '$_POST[username]'");
		while($get_user = mysql_fetch_assoc($get_user_list)) {
			echo "insert into userinfo (fname, lname, username, email) values ('$get_user[fname]', '$get_user[lname]', '$get_user[username]', '$get_user[email]');";
		}
		$get_metric_list = mysql_query("select * from metric");
		while($get_metric = mysql_fetch_assoc($get_metric_list)) {
			echo "insert into metric (name, metricid) values ('$get_metric[name]', $get_metric[metricid]);";
		}
	}
	else
		echo "Invalid account credentials.";
}
?>