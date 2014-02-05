<?php
App::import('Controller','_base/Imgs');
class PostimgsController extends ImgsController{
	var $name = 'Postimgs';
	var $uses = array('Postimg','Post');
}
?>