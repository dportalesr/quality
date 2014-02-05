<?
if($quote = $this->requestAction('/quotes/last/limit:1/order:rand')){
	echo $html->div(null,
		$html->para(null,'Frase del día').
		$html->div('quote',$quote[0]['Quote']['frase'])
	,array('id'=>'quote'));
}
?>