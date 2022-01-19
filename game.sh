while :
do
date=`date +%Y%m%d`
abcd=`sqlplus -s monitorp/password <<EOF
set head off
set feedback off
set pages 0
spool $HOME/eventMail/card/modulecard.txt
select EVENT_MODULE from event_params group by EVENT_MODULE;
spool off
exit;
EOF`
if(! -d $HOME/eventMail/logs/$date)
	then mkdir $HOME/eventMail/logs/$date
fi
for i in `cat $HOME/eventMail/card/modulecard.txt`
do
$HOME/eventMail/game.php $i >>$HOME/eventMail/logs/$date/$i'_Event_'$date.log
done
sleep 30
done
