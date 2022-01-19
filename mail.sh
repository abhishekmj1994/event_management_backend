while :
do
date=`date +%Y%m%d` 
mkdir -p $HOME/eventMail/logs/$date
$HOME/eventMail/mail.php >>$HOME/eventMail/logs/$date/mail_$date.log
sleep 30
done
