game_running=`ps -ef|grep /home/monitorp/eventMail/game.sh | grep -v grep|wc -l`
mail_running=`ps -ef|grep /home/monitorp/eventMail/mail.sh | grep -v grep|wc -l`
if [ $game_running -ge 1 ]
then
	echo "Game Already RUNNING Previously  !!!" 
else
	nohup /home/monitorp/eventMail/game.sh >>/dev/null 2>&1 &
	echo "Game SuccesFully Ran !!" 
fi
if [ $mail_running -ge 1 ]
then
	echo "Mail Already RUNNING Previously  !!!" 
else
	nohup /home/monitorp/eventMail/mail.sh >>/dev/null 2>&1 &
	echo "Mail SuccesFully Ran !!" 
fi
