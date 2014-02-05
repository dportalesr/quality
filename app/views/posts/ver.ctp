<?php
fb($related,'$related');
echo
	$this->element('top',array('centered'=>true)),
		$html->link('Regresar',array('controller'=>'posts','action'=>'index'),array('class'=>'back')),
		$html->div('detail'),
			$html->div('new_date'),
				$html->div('month',$util->fdate('%B',$item[$_m[0]]['created'])),
				$html->div('day',$util->fdate('%d',$item[$_m[0]]['created']));

				if(!empty($related['next']))
					echo $html->link('Siguiente',array('id'=>$related['next'][$_m[0]]['slug']),array('class'=>'next'));
		echo			
			'</div>',
			$html->tag('h1',$item[$_m[0]]['nombre'],array('class'=>'title')),
			
			$html->div('clear'),
				$this->element('showcase',array('data'=>$item[$_m[0].'img'],'opts'=>array('nav'=>'in'))),
				$html->div('desc tmce',$item[$_m[0]]['descripcion'].''),
			'</div>',
		'</div>';
?>
	</div>
</div><!-- content -->
<?php
	echo
		$this->element('sidebar'),
	'</div>',
	$html->div('comments_outside'),
		$html->div('centered'),
			$html->div('content'),
				$this->element('share'),
				$this->element('comments'),
			'</div>',
		'</div>',
	'</div>';
?>