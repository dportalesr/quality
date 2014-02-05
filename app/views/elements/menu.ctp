<?php
echo
	$html->tag('ul',null,array('id'=>'menu')),
		$html->tag('li',$html->link($html->tag('span', 'inicio'),'/'),array('class'=>$this->params['controller']=='inicio' ? 'mSelected' : ''));

		foreach(Configure::read('Modules') as $cntllr => $mod){
			if(isset($mod['menu']) && $mod['menu']){
				echo
					$html->tag('li',
						$html->link(
							$html->tag('span',$mod['menu']),
							array('controller'=>$cntllr,'action'=>'index')
						),
						array('class'=>$this->params['controller'] == $cntllr ? 'mSelected' : '')
					);
			}
		}
	
	echo '</ul>';
?>