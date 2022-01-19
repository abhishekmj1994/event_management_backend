#!/usr/bin/php
<?php
date_default_timezone_set("Asia/Kolkata");
error_reporting(1);
class connection{
	function connectivity()
	{
		$con = oci_connect("bla bla","nope","opdb");
		return $con;
	}
}
class dbMail extends connection{
function styleText(){
$fullstyle = <<<STYLE
<style>
.glow {
	animation: glowing 1500ms infinite;
}
</style>
STYLE;
return $fullstyle;
}

function PrintHtml($body,$event_severity){
$style = $this->styleText();
$fullbody = <<<HTML
<!DOCTYPE HTML>
{$style}
<span style="font-size:20px;font-family:Baskerville Old Face;">Dear Team,</span> <br><br>
<div class="row">	
	<div style="width:100%">
		<div>
			<div style="display: inline-block;padding: 6px 12px;margin-bottom: 0;font-weight: 400;line-height: 1.42857143;text-align: center;white-space: nowrap;vertical-align: middle;touch-action: manipulation;background-image: none;border-radius: 4px;color:white;background-color: #ef3400;border-color: #00acd6;">
				{$event_severity} ALERT OCCURANCE
			</div><br>
			<div style="background-color:gold;color:mediumvioletred">
				{$body}
			</div>
			<br><br><br><span style="color:red">!!!  Please Check  !!!</span>
		</div>
	</div>
</div>
</br>
</br>
<div style="text-align:left;font-size:20px;font-family:Brush Script MT;">Thanks & Regards,
</br>
PSO TEAM
</div><br>
This is an Automated Mail By <a style="text-decoration:none;display: inline-block;padding: 6px 12px;margin-bottom: 0;font-weight: 400;line-height: 1.42857143;text-align: center;white-space: nowrap;vertical-align: middle;touch-action: manipulation;background-image: none;border-radius: 4px;color:#1b1b1b;background-color:#00ef31;border-color: #00acd6;" href="https://10.0.6.125:8080/personal/conso/index.php#eventportal">Event Management Systems  [Click]</a>. Please Do not Reply to it.<br>
</HTML>
HTML;
return $fullbody;
}
function mail_sent(){
	$var=parent::connectivity();
	//$getargs = explode("@",$arguments);
	//print_r($getargs);
	$query=oci_parse($var,"select * from mailpush where sent_time is NULL ");
	oci_execute($query);
	$filecount=1;
	while($data=oci_fetch_assoc($query)){
		$edate=$data['EDATE'];
		$event_id=$data['EVENT_ID'];
		$event_severity=$data['EVENT_SEVERITY'];
		$mailbody=$this->PrintHtml($data['EVENT_DESC'],$event_severity);
		$event_module=$data['EVENT_MODULE'];
		//$filename='/home/monitorp/eventMail/mail/'.preg_replace("/[^A-Za-z0-9]/","_",$event_module).'_'.$filecount.'.txt';
		$filename='/home/monitorp/eventMail/mail/'.$event_id.'.txt';
		$fromto=file_get_contents("fromto.card").PHP_EOL;
		$subject="Subject:[".$event_severity."]".$event_module."_ALERT".PHP_EOL;
		$mime=file_get_contents("mime.card").PHP_EOL;
		file_put_contents($filename,""); //touch the file
		file_put_contents($filename,$fromto,FILE_APPEND); //from and to
		file_put_contents($filename,$subject,FILE_APPEND); //subject dynamic
		file_put_contents($filename,$mime,FILE_APPEND); //Mime 1.0
		file_put_contents($filename,$mailbody,FILE_APPEND); //fullbody
		$query1=oci_parse($var,"update mailpush set sent_time=current_timestamp where edate='$edate' and event_id='$event_id' and sent_time is NULL ");
		$queryRes=oci_execute($query1);	
		if($queryRes){
			//exception throw basis of time and condition and send mail
			$mailSent=shell_exec("/home/monitorp/eventMail/sendmail.sh $filename");
			$mailmsg=date("Ymd H:i:s ").$data['EVENT_MODULE']." Mail has been Sent !!";
		}
		else{
			$mailmsg=date("Ymd H:i:s ").$data['EVENT_MODULE']." Mail has not been Sent !! ";
		}
		$filecount++;
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

