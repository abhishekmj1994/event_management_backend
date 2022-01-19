#!/usr/bin/php
<?php
date_default_timezone_set("Asia/Kolkata");
error_reporting(1);
class connection{
	function connectivity()
	{
		$con = oci_connect("bla bla","noep","opdb");
		return $con;
	}
}
class dbMail extends connection{
		function mail_sent(){
			$var=parent::connectivity();
			//$getargs = explode("@",$arguments);
			//print_r($getargs);
			$query=oci_parse($var,"select * from mailpush where sent_time is NULL ");
			oci_execute($query);
			while($data=oci_fetch_assoc($query)){
				$edate=$data['EDATE'];
				$event_id=$data['EVENT_ID'];
				$event_severity=$data['EVENT_SEVERITY'];
				$mailbody=$this->PrintHtml($data['EVENT_DESC'],$event_severity);
				$event_module=$data['EVENT_MODULE'];
				$filename='/home/monitorp/eventMail/mail/'.preg_replace("/[^A-Za-z0-9]/","_",$event_module).'.txt';
				$mailFile=file_put_contents($filename,$mailbody);
				$subject="[".$event_severity."]".$event_module."_ALERT";
				//echo "/home/monitorp/eventMail/mail.pl $filename $subject";
				$query1=oci_parse($var,"update mailpush set sent_time=current_timestamp where edate='$edate' and event_id='$event_id' and sent_time is NULL ");
				$queryRes=oci_execute($query1);	
				if($queryRes){
					//exception throw basis of time and condition
					$mailSent=shell_exec("/home/monitorp/eventMail/mail.pl $filename '$subject'");
					$mailmsg=date("Ymd H:i:s ").$data['EVENT_MODULE']." Mail has been Sent !!";
				}
				else{
					$mailmsg=date("Ymd H:i:s ").$data['EVENT_MODULE']." Mail has not been Sent !! ";
				}
			}
			if($mailmsg==''){
				return date("Ymd H:i:s")." No mail is to be Sent Now";
			}
			else{
				return $mailmsg;
			}
		}
	}
	$mail = new dbMail();
	echo $mail->mail_sent();
	
?>

