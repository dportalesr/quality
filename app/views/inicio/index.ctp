<?php
echo
	$this->element('service_list'),
	$html->div('showcase_wrapper',$this->element('showcase',array('data'=>$carrusel,'opts'=>array('nav'=>'in')))),
	$html->div('content'),
		$html->div('pad'),
			$html->div('intro'),
				$html->div('intro_text'),
					$html->div('desc tmce',$about['About']['intro'].''),
					$html->div('more',$html->link('Leer más',array('controller'=>'about','action'=>'index')));
?>
				</div>
			</div>
		</div>
	</div>
<?php echo $this->element('sidebar') ?>