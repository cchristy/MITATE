<?php
if($_GET['username']!="" && $_GET['password']!="" && $_GET['phone_number']!="" && $_GET['device_name']!="") {
	$xml = simplexml_load_file("config.xml");
	$dbhostname = $xml->databaseConnection->serverAddress;
	$dbusername = $xml->databaseConnection->user;
    $dbpassword = $xml->databaseConnection->password;
    $dbschemaname = $xml->databaseConnection->name;
	$passwordEncryptionKey = $xml->database->passwordEncryptionKey;
    $dbconnection = mysql_connect($dbhostname, $dbusername, $dbpassword);
    if (!$dbconnection)	{
        die('Website down for maintenance. We will be live soon.');
    }
    mysql_select_db($dbschemaname, $dbconnection);
	$encrypted_password = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($passwordEncryptionKey), base64_decode($_GET[password]), MCRYPT_MODE_CBC, md5(md5($passwordEncryptionKey))));
	$loginresultset = mysql_query("SELECT count(*) as status FROM userinfo where username = '$_GET[username]' and password = '$encrypted_password' and status = 1");
    if ($loginresultset) {
		$loginresultrow = mysql_fetch_assoc($loginresultset);
        if ($loginresultrow['status'] == "1") {		
			$record_found = 0;
			$user_phone_number = $_GET[phone_number];
			$deviceid_from_database = "";
			$encrypted_user_phone_number = md5($user_phone_number);
			$get_deviceid_strings = mysql_query("SELECT deviceid, pollinterval from userdevice where username = '$_GET[username]' and random_String = '$encrypted_user_phone_number'");
			while($fetch_deviceid_strings = mysql_fetch_assoc($get_deviceid_strings))
     		{							
					$record_found = 1;
					echo $fetch_deviceid_strings[deviceid] . ":" . ($fetch_deviceid_strings[pollinterval] * 60 * 1000);
			}
			if($record_found == 0) {
				$random_string = 1000000000;
				$get_random_string_counts = mysql_query("SELECT count(*) as count, max(deviceid) as maxval from userdevice");
				while($fetch_random_string_count = mysql_fetch_assoc($get_random_string_counts)) {
					if($fetch_random_string_count[count] > 0)
						$random_string = $fetch_random_string_count[maxval] + 1;
				}
				$deviceid_short = md5($user_phone_number);	
				$sql_store_deviceid ="INSERT INTO userdevice (username, devicename, pollinterval, deviceid, minbatterypower, random_string) VALUES('$_GET[username]', '$_GET[device_name]', 30, $random_string, 5, '$deviceid_short')";
				if (!mysql_query($sql_store_deviceid, $dbconnection)) {die('Website down for maintenance. We will be live soon.');}					
				echo $random_string . ":1800000";
			}	
		}
		else
			echo "InvalidLogin";
	}
}
?>