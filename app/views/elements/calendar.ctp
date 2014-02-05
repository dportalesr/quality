<?
	$month = isset($month) && $month ? $month : date('n'); 
	$year = isset($year) && $year ? $year : date('Y'); 
	
	/////////////////////////////////
	
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	$today = date('d');
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1; # Primera semana del mes
	$day_counter = 0;
	$dates_array = array();
	$scripts = '';

	$headings = array('Do','Lu','Ma','Mi','Ju','Vi','Sa');
	$calendar.= '<tr class="calendar-row"><td colspan="7" class="calendar-month">'.ucfirst(strftime('%B')).'</td></tr>';
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-name">'.implode('</td><td class="calendar-day-name">',$headings).'</td></tr><tr class="calendar-row">';

	for($x = 0; $x < $running_day; $x++,$days_in_this_week++) # Espacios en blanco (días del mes anterior)
		$calendar.= $html->tag('td',' ',array('class'=>$x ? 'calendar-day':'calendar-sunday'));

	for($cur_day = 1; $cur_day <= $days_in_month; $cur_day++){

		$calendar.= '<td class="'.(!date('w',mktime(0,0,0,$month,$cur_day,$year))?'calendar-sunday':'calendar-day').($cur_day==$today?' calendar-today':'').(in_array($cur_day,$data)?' calendar-events':'').'">';
		$calendar.= in_array($cur_day,$data) ? $html->link($cur_day,'/events/listevents/'.$cur_day.'/'.$month.'/?height=560&width=450',array('class'=>'smoothbox')): $cur_day;
		$calendar.= '</td>';

		if($running_day == 6){
			$calendar.= '</tr>'; # Termina semana
			if(($day_counter+1)!=$days_in_month) $calendar.= '<tr class="calendar-row">';# Si no es último día del mes, nueva fila
			$running_day = -1;
			$days_in_this_week = 0;
		}
		$days_in_this_week++; $running_day++; $day_counter++;
	}

	if($days_in_this_week < 8){ # Espacios al final
		for($x = 1; $x <= (8 - $days_in_this_week); $x++)
			$calendar.= '<td class="calendar-day"> </td>';
	}

	$calendar.= '</tr></table>';

	echo $calendar;
	echo $html->css('calendar',null,null,0);
?>