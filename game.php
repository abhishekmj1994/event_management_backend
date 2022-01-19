#!/usr/bin/php
<?php
	$nohup_module=$argv[1];
	date_default_timezone_set("Asia/Kolkata");
	error_reporting(1);
	class connection{
		function connectivity()
		{
			$con = oci_connect("blabla","putyourown","opdb");
			return $con;
		}
	}
	class dbinsEvent extends connection {
		
		function rec_insert($arguments,$event_severity,$output){
			$var=parent::connectivity();
			$getargs = explode("@",$arguments);
			//print_r($getargs);
			$edate=date("Ymd");
			$st_time=date("H:i");
			$end_time="NA";
			$server_name=$getargs[4];
			$module_name=$getargs[2];
			$event_id=$getargs[0];
			//$event_desc=$getargs[1].' at '.$getargs[4];
			$event_desc=$getargs[1].' :-- '.$output.' at '.$getargs[4];
			//file_put_contents("/opt/hpws22/apache/htdocs/pace/Online_portal/pages/card_folder/notilog.txt",$event_desc.PHP_EOL,FILE_APPEND);
			$event_status="OPEN";
			
			$query=oci_parse($var,"insert into event_master_testing (edate,st_time,end_time,server_name,module_name,event_id,event_desc,event_severity,event_status) VALUES ('$edate','$st_time','$end_time','$server_name','$module_name','$event_id','$event_desc','$event_severity','$event_status')");
			$queryRes=oci_execute($query);
			if($queryRes){return date("Ymd H:i:s")." $module_name Events have been inserted !!";}
			else{return date("Ymd H:i:s")." $module_name Events failed to insert !!";}
		}
		function rec_update($arguments,$event_severity,$event_status){
			$var=parent::connectivity();
			$getargs = explode("@",$arguments);
			//print_r($getargs);
			$edate=date("Ymd");
			$end_time=date("H:i");
			$event_id=$getargs[0];
			$module_name=$getargs[2];
			
			//checking needed only in these Zone 20181028
			if($event_status=='OPEN'){
			$query=oci_parse($var,"update event_master_testing set event_severity='$event_severity' where edate='$edate' and event_id='$event_id' and event_status='$event_status' ");
			//echo "update event_master_testing set event_severity='$event_severity' where edate='$edate' and event_id='$event_id' and event_status='$event_status'".PHP_EOL;
			$info='Opened';
			}
			//currently not being used
			elseif($event_status=='CLOSE'){
				$query=oci_parse($var,"update event_master_testing set event_status='$event_status',end_time='$end_time' where edate='$edate' and event_id='$event_id' and event_severity='$event_severity' and rowid = (select ROWID from (select * from event_master_testing where event_status='OPEN' and edate='$edate' and event_id='$event_id' order by ST_TIME desc ) where rownum < 2) ");
				//echo "update event_master_testing set event_status='$event_status',end_time='$end_time' where edate='$edate' and event_id='$event_id' and event_severity='$event_severity' and rowid = (select ROWID from (select * from event_master_testing where event_status='OPEN' and edate='$edate' and event_id='$event_id' order by ST_TIME desc ) where rownum < 2) ".PHP_EOL;
				$info='Closed';
			}
			$queryRes=oci_execute($query);
			if($queryRes){return date("Ymd H:i:s")." $module_name Events have been $info $event_id !! ";}
			else{return date("Ymd H:i:s")." $module_name Events failed to update $event_id !!";}
		}
		function rec_close($arguments,$event_severity,$event_status){
			$var=parent::connectivity();
			$getargs = explode("@",$arguments);
			//print_r($getargs);
			$edate=date("Ymd");
			$end_time=date("H:i");
			$event_id=$getargs[0];
			$module_name=$getargs[2];
			$query=oci_parse($var,"update event_master_testing set event_severity='$event_severity',event_status='$event_status',end_time='$end_time' where edate='$edate' and event_id='$event_id' and event_status!='$event_status'");
			//echo "update event_master_testing set event_severity='$event_severity',event_status='$event_status',end_time='$end_time' where edate='$edate' and event_id='$event_id' and event_status!='$event_status' ".PHP_EOL;
			$queryRes=oci_execute($query);
			if($queryRes){return date("Ymd H:i:s")." $module_name Events have been closed $event_id !! ";}
			else{return date("Ymd H:i:s")." $module_name Events failed to close $event_id !!";}
		}
		function rec_update_desc($arguments,$event_severity,$output){
			$var=parent::connectivity();
			$getargs = explode("@",$arguments);
			//print_r($getargs);
			$edate=date("Ymd");
			$event_id=$getargs[0];
			$module_name=$getargs[2];
			$event_desc=$getargs[1].' :-- '.$output.' at '.$getargs[4];
			//file_put_contents("/opt/hpws22/apache/htdocs/pace/Online_portal/pages/card_folder/notilog.txt",$event_desc.PHP_EOL,FILE_APPEND);
			$query=oci_parse($var,"update event_master_testing set event_desc='$event_desc',event_severity='$event_severity' where edate='$edate' and event_id='$event_id' and EVENT_STATUS='OPEN'");
			//echo "update event_master_testing set event_desc='$event_desc' where edate='$edate' and event_id='$event_id' and EVENT_STATUS='OPEN' and EVENT_SEVERITY='$event_severity'".PHP_EOL;
			$queryRes=oci_execute($query);
			if($queryRes){return date("Ymd H:i:s")." $module_name Events Description have been Updated $event_id !! ";}
			else{return date("Ymd H:i:s")." $module_name Events failed to change description of $event_id !!";}
		}
		function rec_check_query($arguments,$event_severity){
			$var=parent::connectivity();
			$getargs = explode("@",$arguments);
			//print_r($getargs);
			$edate=date("Ymd");
			$event_id=$getargs[0];
			$event_status="OPEN";
			//echo "select * from event_master_testing where event_status='$event_status' and edate='$edate' and event_id='$event_id' and event_severity='$event_severity'".PHP_EOL;
			$queryCount=oci_parse($var,"select * from event_master_testing where event_status='$event_status' and edate='$edate' and event_id='$event_id'");		//query goes here 			
			oci_execute($queryCount);
			oci_fetch_all($queryCount,$out);
			return oci_num_rows($queryCount);
		}
		function rec_select($args){
			$nohup_module=$args;
			$var=parent::connectivity();
			$query=oci_parse($var,"select * from event_params where EVENT_MODULE='$nohup_module'");
			oci_execute($query);
			while($data=oci_fetch_assoc($query)){
				$array[]=date("Ymd H:i:s").'@'.$data['EVENT_ID'].'@'.$data['EVENT_NAME'].'@'.$data['EVENT_MODULE'].'@'.$data['EVENT_COMMAND'].'@'.$data['LOCATION'].'@'.$data['THRESHOLD_1'].'@'.$data['THRESHOLD_2'].'@'.$data['THRESHOLD_3'].'@'.$data['MATURITY_TIME'].'@'.$data['EXCEPTION'];
				//exception
				//$array_exception[]=date("Ymd H:i:s").'@'.$data['EVENT_ID'].'@'.$data['EVENT_NAME'].'@'.$data['EVENT_MODULE'];
			}
			file_put_contents("/home/monitorp/eventMail/test/game_test_$args.txt",implode("\n",$array));
			//exception put into file modulewise
			//file_put_contents("/home/monitorp/eventMail/exception/exception_$args.txt",implode("\n",$array_exception));
		}
		function maturity_checker($arguments,$event_severity){
			$var=parent::connectivity();
			$getargs = explode("@",$arguments);
			$maturity=$getargs[8];
			$tripleMaturity=3*(intval($getargs[8]));
			$current_time=date("H:i");
			$maturityTimeConversion=strtotime($maturity) - strtotime("00:00:00");
			$edate=date("Ymd");
			$event_id=$getargs[0];
			$event_status="OPEN";
			$queryCount=oci_parse($var,"select * from (select * from event_master_testing where event_status='$event_status' and edate='$edate' and event_id='$event_id' and event_severity = '$event_severity' order by ST_TIME desc ) where rownum < 2");	
			//query goes here 			
			oci_execute($queryCount);
			$x = oci_fetch_assoc($queryCount);
			oci_fetch_all($queryCount,$out);
			$TotalTimeConverion = $maturityTimeConversion + strtotime($x['ST_TIME']);
			
			/*added on 20181109 for mail noise control
			$mailCountQuery=oci_parse($var,"select count(*) as mailcount from mailpush where SENT_TIME > systimestamp - numtodsinterval($tripleMaturity,'MINUTE') and EVENT_ID='$event_id' and edate='$edate'");
			oci_execute($mailCountQuery);
			$counter=oci_fetch_assoc($mailCountQuery);
			//echo 'count->'.$counter['MAILCOUNT'].' query->'."select count(*) as mailcount from mailpush where SENT_TIME > systimestamp - numtodsinterval($tripleMaturity,'MINUTE') and EVENT_ID='$event_id' and edate='$edate'".PHP_EOL;
			$mailcounter=$counter['MAILCOUNT'];
			finished and checked  and removed on 20181109*/
			
			if(oci_num_rows($queryCount)>0  && ($TotalTimeConverion < strtotime(date("H:i")))){
				return '1';
				//return array("timeStrTotal" =>$TotalTimeConverion,"curdate" =>strtotime(date("H:i")),"maturity" =>$maturity,"ST_Time"=>$x['ST_TIME']);
			}
			else{
				return '0';
			}
			
		}
		function mailDB($arguments,$event_severity,$output){
			$var=parent::connectivity();
			$getargs = explode("@",$arguments);
			$edate=date("Ymd");
			$tripleMaturity=3*(intval($getargs[8]));
			$st_time=date("H:i");
			$event_id=$getargs[0];
			$event_module=$getargs[2];
			$event_desc=$getargs[1].' :-- '.$output.' at '.$getargs[4].' on '.$st_time;
			//$query=oci_parse($var,"select * from mailpush where EVENT_ID = '$event_id' and SENT_TIME='NA' and EDATE='$edate' and rownum < 2 order by mail_id desc");
			$query=oci_parse($var,"select * from ( select * from mailpush where  EVENT_ID = '$event_id' and EDATE='$edate' order by SENT_TIME desc ) where rownum < 2 and SENT_TIME is NULL ");
			oci_execute($query);
			oci_fetch_all($query,$out);
			$numRows=oci_num_rows($query);
			
			
			//added on 20181109 for mail noise control
			$mailCountQuery=oci_parse($var,"select count(*) as mailcount from mailpush where SENT_TIME > systimestamp - numtodsinterval($tripleMaturity,'MINUTE') and EVENT_ID='$event_id' and edate='$edate'");
			oci_execute($mailCountQuery);
			$counter=oci_fetch_assoc($mailCountQuery);
			//echo 'count->'.$counter['MAILCOUNT'].' query->'."select count(*) as mailcount from mailpush where SENT_TIME > systimestamp - numtodsinterval($tripleMaturity,'MINUTE') and EVENT_ID='$event_id' and edate='$edate'".PHP_EOL;
			$mailcounter=$counter['MAILCOUNT'];
			//finished and checked on 20181028
			
			//echo "insert into mailpush (event_id,event_module,event_desc,sent_time,edate) VALUES ('$event_id','$event_module','$event_desc',NULL,'$edate')";	
			if($numRows == 0 && $mailcounter < 4){
				$queryInsert=oci_parse($var,"insert into mailpush (event_id,event_module,event_desc,sent_time,edate,event_severity) VALUES ('$event_id','$event_module','$event_desc',NULL,'$edate','$event_severity')");
				oci_execute($queryInsert);
			}
			if($queryInsert){return date("Ymd H:i:s")." $event_module Events have been inserted in mailpush $event_id !!";}
			else{return date("Ymd H:i:s")." $event_module Events failed to inserted in mailpush due to High Mail Count in last $tripleMaturity minutes !!";}
		}
	}
	//OBJECT CREATION
	$DBins = new dbinsEvent();
	
	
	//code start
	//file_put_contents("/opt/hpws22/apache/htdocs/pace/Online_portal/pages/card_folder/notilog.txt","");
	$region = trim(shell_exec("cat /opt/hpws/apache/cgi-bin/trials/bip/data/TESTING/sys_stat1.txt"));
	$flowCount = intval(trim(shell_exec("cat /opt/hpws/apache/cgi-bin/trials/bip/data/TESTING/currFlow.seq | wc -l")));
	if($region == "D" && $flowCount >= 3 && $flowCount <=8){
		$DBins->rec_select($argv[1]);
		foreach(file("/home/monitorp/eventMail/test/game_test_".$argv[1].".txt") as $index=>$data){
			$mainColdata=explode("@",$data);
			array_shift($mainColdata);
			$mainCommand=str_replace("YYYYMMDD",date("Ymd"),$mainColdata[3]);
			$output=trim(shell_exec($mainCommand));
			//echo 'Output type is : '.gettype($output).' - > '.$output.PHP_EOL;
			//varchar threshold
			if(!is_numeric($output)){
				//echo "INSIDE IF VARCHAR LOOP!";
				if(trim($output)==$mainColdata[7] || stristr(trim($output),"ERROR")){
					$event_severity='HIGH';
					$count=$DBins->rec_check_query(implode("@",$mainColdata),$event_severity);
					if($count==0){
						//mailDB insertion
						$msg=$DBins->rec_insert(implode("@",$mainColdata),$event_severity,$output);
						$msg1=$DBins->mailDB(implode("@",$mainColdata),$event_severity,$output);
					}
					else{
						$msg=$DBins->rec_update_desc(implode("@",$mainColdata),$event_severity,$output);
						$msg1=$DBins->mailDB(implode("@",$mainColdata),$event_severity,$output);
					}
				}
				elseif(trim($output)==$mainColdata[6]){
					$event_severity='MEDIUM';
					//count is working checked ok
					$count=$DBins->rec_check_query(implode("@",$mainColdata),$event_severity);
					if($count==0){	
						//Only Event insertion
						$msg=$DBins->rec_insert(implode("@",$mainColdata),$event_severity,$output);
					}
					//maturity still in testing every condition is going into open
					else{
						$levelup=$DBins->maturity_checker(implode("@",$mainColdata),$event_severity);
						if($levelup=='1'){
							$event_severity='HIGH';
							$msg=$DBins->rec_update_desc(implode("@",$mainColdata),'MEDIUM',$output);
							//$msg=$DBins->rec_update(implode("@",$mainColdata),'MEDIUM','OPEN');
							//Stop varchar medium maturity to be HIGH EVENT
							//$msg1=$DBins->mailDB(implode("@",$mainColdata),$event_severity,$output);
						}
						else{
							$msg=$DBins->rec_update_desc(implode("@",$mainColdata),'MEDIUM',$output);
						}
					}
				}
				//event closure
				elseif(trim($output)==$mainColdata[5] ){
					$event_severity='LOW';					
					$count=$DBins->rec_check_query(implode("@",$mainColdata),$event_severity);
					if($count > 0){
						$msg=$DBins->rec_close(implode("@",$mainColdata),$event_severity,'CLOSE');
					}
				}
			}
			//integer threshold
			else{
				//echo "INSIDE ELSE INTEGER LOOP!";
				if(trim($output) > $mainColdata[7]){
					$event_severity='HIGH';
					$count=$DBins->rec_check_query(implode("@",$mainColdata),$event_severity);
					if($count==0){
						//mailDB insertion
						$msg=$DBins->rec_insert(implode("@",$mainColdata),$event_severity,$output);
						$msg1=$DBins->mailDB(implode("@",$mainColdata),$event_severity,$output);
					}
					else{
						$msg=$DBins->rec_update_desc(implode("@",$mainColdata),$event_severity,$output);
						$msg1=$DBins->mailDB(implode("@",$mainColdata),$event_severity,$output);
					}
				}
				elseif(trim($output) > $mainColdata[6]){
					$event_severity='MEDIUM';
					$count=$DBins->rec_check_query(implode("@",$mainColdata),$event_severity);
					if($count==0){
						//Only Event insertion
						$msg=$DBins->rec_insert(implode("@",$mainColdata),$event_severity,$output);
					}
					//maturity still in testing
					else{
						$levelup=$DBins->maturity_checker(implode("@",$mainColdata),$event_severity);
						if($levelup=='1'){
							$event_severity='HIGH';
							$msg=$DBins->rec_update_desc(implode("@",$mainColdata),'MEDIUM',$output);
							$msg1=$DBins->mailDB(implode("@",$mainColdata),$event_severity,$output);
						}
						else{
							$msg=$DBins->rec_update_desc(implode("@",$mainColdata),'MEDIUM',$output);
						}
					}
				}
				elseif(trim($output) > $mainColdata[5]){
					$event_severity='LOW';
					$count=$DBins->rec_check_query(implode("@",$mainColdata),$event_severity);
					if($count==0){
						$msg=$DBins->rec_insert(implode("@",$mainColdata),$event_severity,$output);
					}
					else{
						$msg=$DBins->rec_update_desc(implode("@",$mainColdata),$event_severity,$output);
					}
				}
				//event closure
				else{
						$event_severity='LOW';
						$count=$DBins->rec_check_query(implode("@",$mainColdata),$event_severity);
						if($count > 0){
							$msg=$DBins->rec_close(implode("@",$mainColdata),$event_severity,'CLOSE');
						}
				}
			}
		}
	}
	else{
		echo shell_exec('echo CUT OFF');
		$msg="CUT OFF Done";
	}
	if($msg=='' && $msg1==''){
		echo date("Ymd H:i:s")." No events in the ".$argv[1]." Module".PHP_EOL;
	}
	else{
		echo $msg.PHP_EOL; //inserted in event_master_testing
		echo $msg1; //inserted in mailpush
	}
/*
after 20181025 also first event was getting closed and also all the end_time was being closed as same every time updation is done.=========> eradicated the issue
mail noise control -> no mail.php change is needed though this code upto 5mail for a same thing can go in 3 times of the threshold maturity time dynamically
rec closure new function additon removing the dependency of rec_update permanently.
*/
?>
