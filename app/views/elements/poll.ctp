<?php
$full = $this->params['controller']=='polls';
if($poll){
	$hide = '';
	echo
		$form->create('Question',array('url'=>array('controller'=>'polls','action'=>'vote'),'id'=>'poll_form')),
			$html->div('questions');
		
			foreach($poll['Question'] as $question){
				echo
					$html->div('question '.$hide,null,array('id'=>'question_'.$question['id'])),
					$html->div('title title3',$question['nombre']);
				
				foreach($question['Answer'] as $answer){
					echo $form->submit($answer['nombre'],array(
						'id'=>'p_'.$question['id'].'_a_'.$answer['id'],
						'class'=>'answer',
						'title'=>$answer['nombre'],
						'name'=>'data[Question][ids]['.$question['id'].'_'.$answer['id'].']'
					));
				}
				
				echo '</div>';
				if(!$full) $hide = 'hide';
			}
		echo
			$html->div('question hide omega',$html->div('title title3','¡Gracias por participar!')),
			$form->input('aid',array('value'=>'','type'=>'hidden')),
			$form->input('qid',array('value'=>'','type'=>'hidden')),

			'</div>', // .questions
		$form->end();
		
	$moo->buffer('$$("#poll_form .question").set("reveal",{transition:"pow:out"}); ');
	$moo->addEvent('#poll_form .answer','click','$("QuestionAid").set("value",this.id.split("_")[3]);$("QuestionQid").set("value",this.id.split("_")[1]);',array('prevent'=>true,'url'=>'/polls/vote'.($full ? '/1':''),'data'=>'"poll_form"','css'=>1,'spinner'=>array($full ? 'this.getParent(".question")':'"poll_form"')));
}
?>