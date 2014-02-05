<?php
$autor = $data['web'] ? $html->link($data['autor'],$data['web'],array('rel'=>'nofollow','target'=>'_blank')):$data['autor'];

echo
	$html->div('comment thumb'.(isset($odd) && $odd ? ' odd':''),null,array('id'=>'comment_'.$data['id'])),
		$html->link('','',array('name'=>'comment_'.$data['id'])),
		$html->div('title',$autor),
		$html->para('date',$util->fdate('l',$data['created'])),
		$html->div('desc',$util->txt($data['descripcion'])),
'</div>';

if($this->params['isAjax']){ ?>
	<script type="text/javascript">
		var new_comment = $("comment_<?=$data["id"]?>").inject('comment_count','after');
		var comment_form = $("comments").getElement("form");
		comment_form.reset();
		formtips.detach(".input_error");
		comment_form.getElements(".input_error").removeClass("input_error");
		new Fx.Slide(new_comment, {duration: 800}).hide().slideIn();
		new Fx.Scroll(window,{ duration:900,transition:Fx.Transitions.Quint.easeInOut }).toElement(new_comment);
	</script>
<? } ?>