<?php
if($this->Session->check('Message.flash')){
	echo
		$this->Session->flash(),
		$moo->pop();
}
?>