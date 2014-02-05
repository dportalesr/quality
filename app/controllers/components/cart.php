<?php
class CartComponent extends Object {
	var $components = array('Cookie','Session','Paypal','Email');
	var $out_of_stock = array();
	var $item_model = 'Product';
	var $currency = 'MXN';

	function initialize(&$controller) {
		$this->controller =& $controller;
		$this->item_model = $this->controller->uses[0];
		$this->Product = $this->controller->{$this->item_model};
		$this->Order = $this->controller->Order;
	}
/**
 * Adds an item to the cart session list
 * @return 
 */
	function add2cart(){
		$item_id = $this->controller->data[$this->item_model]['id'];
		$type_id = false;
		
		if(!empty($this->controller->data[$this->item_model]['type']))
			$type_id = $this->controller->data[$this->item_model]['type'];

		if(!empty($item_id)){
			$item = $this->Product->find_(array(
				$item_id,
				'contain'=>array($this->item_model.'portada'),
				'fields'=>array('id','nombre','slug','precio',$this->item_model.'portada.src')
			),'first');
			
			if($item){
				$item['Type'] = false;

				if((!empty($type_id)) && $type = $this->Product->Type->find_(array($type_id,'contain'=>false,'fields'=>array('id','nombre','precio')))){
					$item_id.= '_'.$type_id;
					$item['Type'] = $type['Type'];
					if(!empty($type['Type']['precio']))
						$item[$this->item_model]['precio'] = $type['Type']['precio'];
				}

				if($this->Session->check('cart.items.'.$item_id)){ // +1
					$this->Session->write('cart.items.'.$item_id.'.qty',$this->Session->read('cart.items.'.$item_id.'.qty')+1);
					$this->response(__('item_agregacion_adicional',true));
				} else { // New item
					$item['qty'] = 1;
					$this->Session->write('cart.items.'.$item_id,$item);
					$this->response(__('item_agregacion_exitosa',true));
				}
				return;
			}
		}

		$this->response(__('item_agregacion_fallida',true));
	}
/**
 * Updates the Cart item list at Session var
 * @param  boolean $respond If it returns a response
 * @return [type]
 */
	function updateqty($respond = true){
		if(!empty($this->controller->data[$this->item_model])){
			foreach($this->controller->data[$this->item_model] as $item_key => $item){
				if($this->Session->check('cart.items.'.$item_key)){
					$this->Session->write('cart.items.'.$item_key.'.qty',(int)$item['qty']);
				}
			}
			
			$this->update_response($respond);			
		}
	}

	function remove(){
		if(!empty($this->controller->data['remove'])){
			foreach ($this->controller->data['remove'] as $item_id => $value) {
				$this->Session->delete('cart.items.'.$item_id);
			}

			$this->update_response();			
		}
	}
/**
 * Returns the JS response on every change in the cart
 * @param  boolean $respond     [description]
 * @param  boolean $update_cart [description]
 * @return [type]
 */
	function update_response($respond = true, $update_cart = true){
		if($respond){
			$response = '';
			$total = 0;

			if($this->Session->check('cart.items')){
				$items = $this->Session->read('cart.items'); fb($items,'$items');
				
				foreach($items as $item_key => $item){
					$item_precio = $this->Session->read('cart.items.'.$item_key.'.'.$this->item_model.'.precio');
					$total+= $item['qty']*$item_precio;
					$response.='$("precio_'.$item_key.'").set("html","'.(number_format($item['qty']*$item_precio,2)).'");';
				}
			}
			
			$response.= '$("cart_total").set("html","'.number_format($total,2).'");';
			$this->response($response,true,$update_cart);
		}
	}

/**
 * Manages the response (User messages and/or JS responses) between ajax or POST requests
 * @param  string  $msg         User message or JS to return as ajax response
 * @param  boolean $js          If the response is JS code or an User message
 * @param  boolean $update_cart If the response includes JS code to update the cart list widget in the view
 * @return 
 */
	function response($msg, $js = false, $update_cart = true){
		if(!empty($this->controller->params['isAjax'])){
			if(!$js) $msg = 'new mooPop("'._enc($msg).'",3600);';
			$ajax = $msg;
			
			if($update_cart)
				$ajax.= ' new Request.HTML({ update:"cart_list_wrapper", url:"/products/cart_list", "onComplete":function(){ $("cart_list_wrapper").highlight(); } }).send();';

			$this->controller->set(compact('ajax'));
			$this->controller->render('js');
		} else {
			$this->flash($msg);
			$this->controller->redirect($this->controller->referer(),true);
		}		
	}

	function checkout(){
		if(empty($this->controller->data['checkout'])){
			/** NO JS **/ if(!empty($this->controller->data[$this->item_model])){ $this->remove();$this->updateqty(false); }
		} else {
			$this->setcheckout();
		}

		$this->controller->set('items',$this->Session->read('cart.items'));
	}

	function setcheckout(){
		$this->updateqty(false);
		$this->items = array();
		$items = $this->Session->read('cart.items');
		$find_opts = array('contain'=>false,'fields'=>array('id','nombre','precio','stock'));
		$this->_order = array('Order'=>array('status'=>'Pendiente','total'=>0,'buyer_id'=>null));

		if($this->Session->check('cart.Buyer.id'))
			$this->_order['Order']['buyer_id'] = $this->Session->read('cart.Buyer.id');

		foreach ($items as $item_id => $item) {
			if(!$item['qty']){
				$this->Session->delete('cart.items.'.$item_id);
				continue;
			}

			$product = $this->Product->find_(array_merge(array($item[$this->item_model]['id']),$find_opts));
			$product[$this->item_model]['nombre'] = ucfirst($product[$this->item_model]['nombre']); # Prettifier
			$type = false;

			if($product && (!empty($item['Type']['id']))){
				$type = $this->Product->Type->find_(array_merge(array($item['Type']['id']),$find_opts));
				$product[$this->item_model]['stock'] = $type['Type']['stock'];
				
				if(!empty($type['Type']['precio']))
					$product[$this->item_model]['precio'] = $type['Type']['precio'];
			}
			
			if(isset($product[$this->item_model]['stock']) && $product[$this->item_model]['stock'] < $item['qty']){ // Out of Stock!
				$this->Session->write('cart.items.'.$item_id.'.qty',$product[$this->item_model]['stock'] ? $product[$this->item_model]['stock'] : 0);
				$this->out_of_stock[$item_id] = $product[$this->item_model]['stock'];
			
			} else {
				$this->items[$item_id] = array(
					'name'=>$product[$this->item_model]['nombre'],
					'desc'=>$type ? $type['Type']['nombre'] : null,
					'amt'=>$product[$this->item_model]['precio'],
					'qty'=>$item['qty'],
				);

				$this->_order['Orderdetail'][] = array(
					strtolower($this->item_model).'_id'=>$product[$this->item_model]['id'],
					'type_id'=>$type ? $type['Type']['id'] : null,
					'cantidad'=>$item['qty']
				);

				$this->_order['Order']['total']+= $product[$this->item_model]['precio']*$item['qty'];

				$this->Paypal->additem($item_id,$this->items[$item_id]);
			}
		}

		if($this->out_of_stock){ // Stock problems
			$this->flash(__('items_vendidos_durante_compra',true));
			$this->Session->write('cart.out_of_stock',$this->out_of_stock);
			return false;

		} else { // Everything went better than expected :)
			// Save Order to Session
			$this->Session->write('cart.current_order',$this->_order);

			$this->Paypal->setCurrencyCode($this->currency);
			if(!$this->Paypal->setExpressCheckout()){
				$this->cancel(__('payment_not_initiated',true));
			}
		}

		$this->controller->set(compact('items'));
	}

	function docheckout($notify = true){
		if($this->Session->check('cart.current_order')){
			$this->_order = $this->Session->read('cart.current_order');
		} else {
			$this->cancel(__('payment_interrupted',true));
		}

		// Recheck for Stock
		$this->out_of_stock = array();
		$find_opts = array('contain'=>false,'fields'=>array('id','stock'));

		if(!empty($this->_order['Orderdetail'])){
			foreach($this->_order['Orderdetail'] as $detail){
				if(!empty($detail['type_id'])){
					$model = $this->Product->Type;
					$item_id = $detail[strtolower($this->item_model).'_id'].'_'.$detail['type_id'];
				} else {
					$model = $this->Product;
					$item_id = $detail[strtolower($this->item_model).'_id'];
				}
				
				$item = $model->find_(array_merge(array($detail[strtolower($model->alias).'_id']),$find_opts));
				if(isset($item[$model->alias]['stock']) && $item[$model->alias]['stock'] < $detail['cantidad']){ // Out of stock!
					$this->out_of_stock[$item_id] = $item[$model->alias]['stock'];
					$this->Session->write('cart.items.'.$item_id.'.qty',$item[$model->alias]['stock']);
				}
			}
		}
		
		if(!empty($this->out_of_stock)){
			$this->flash(__('items_vendidos_durante_compra',true));
			$this->Session->write('cart.out_of_stock',$this->out_of_stock);
			$this->controller->redirect(array('action'=>'checkout'),true);
		}

		// Save order prospect
		if(!$this->Order->saveAll($this->_order,array('validate'=>true))){
			$this->cancel(__('payment_not_saved',true));
		}

		$this->_order['Order']['id'] = $this->Order->id;

		$this->pay_details = $pay_details = $this->Paypal->doExpressCheckoutPayment();
		$request = $this->Paypal->processOutput($this->Paypal->request);
		$response = $this->Paypal->response;
		$payer_data = array(
			'id'=>$this->Order->id,
			'total'=>$response['PAYMENTINFO_0_AMT'],
			'currency'=>$response['PAYMENTINFO_0_CURRENCYCODE'],
			'correlation'=>$response['CORRELATIONID'],
			'payer_id'=>$request['PAYERID'],
			'payer_email'=>$request['EMAIL'],
			'payer_firstname'=>$request['FIRSTNAME'],
			'payer_lastname'=>$request['LASTNAME']
		);
		
		$notify_ = array();
		if(!empty($notify)){
			$notify_ = array($this->_order['Order']['email']);
			
			if(is_array($notify))
				$notify_ = array_merge($notify_,$notify);
		}

		$this->success = true;
		if($pay_details === false){
			$this->success = false;
			$i = 0;
			$errors = array();
			do {
				$errors[] = '['.$response['L_ERRORCODE'.$i].'] '.urldecode($response['L_LONGMESSAGE'.$i]);
				$i++;
			} while(!empty($response['L_ERRORCODE'.$i]));
			
			$this->_order['Order'] = array_merge($this->_order['Order'],array(
				'status'=>'Fallida',
				'errors'=>implode("\n",$errors)
			));

			$this->Order->save($this->_order);

			if(!$this->notify($notify_))
				$errors[] = __('problema_notificacion',true);

			$this->cancel($errors);

		} else {
			if(!empty($this->_order['Orderdetail'])){
				// Stock decrease
				foreach($this->_order['Orderdetail'] as $detail){
					if(!empty($detail['type_id']))
						$this->Product->Type->updateAll(array('Type.stock'=>'Type.stock-'.$detail['cantidad']),array('Type.id'=>$detail['type_id']));
					else
						$this->Product->updateAll(array($this->item_model.'.stock'=>$this->item_model.'.stock-'.$detail['cantidad']),array($this->item_model.'.id'=>$detail[strtolower($this->item_model).'_id']));
				}
			}			

			// Mark order as paid
			$this->_order['Order']['status'] = 'Pagada';
			$this->Order->save($this->_order);

			$this->controller->set('cart_flash',__('payment_success',true));
			$this->Session->write('cart.items',array());
			$this->Session->delete('cart.current_order');

			if(!$this->notify($notify_))
				$this->cancel(__('problema_notificacion',true));
		}
	}

	function flash($msg){ $this->Session->write('cart.flash',$msg); }
	function cancel($error, $cancel_url = array('action'=>'cancelado')){
		$this->flash($error);
		$this->controller->redirect($cancel_url,true);
	}
	function reset(){
		$this->pay_details = null;
		$this->_order = null;
		$this->failures = array();
		$this->Session->delete('cart.current_order');
	}
	function notify($emails, $msg = null){
		$emails = (array)$emails;
		if(is_null($msg)) $msg = $this->success;
		$failure = !(bool)$msg;

		$emails[] = 'ventas@'.Configure::read('Site.domain');

		if($msg === true){
			$msg = array(
				__('payment_success_title',true),
				__('payment_success_body',true)
			);
		} elseif($msg === false){
			$emails[] = 'soporte@pulsem.mx';
			$msg = array(
				__('payment_failed_title',true),
				__('payment_failed_body',true)
			);
		} elseif(!is_array($msg)){
			$msg = (array)$msg;
		}

		$site_domain = Configure::read('Site.domain');
		$site_name = Configure::read('Site.name');
		$payer_email = array_shift($emails);
		$order = $this->_order;


		if($failure){
			$pay_details = $this->pay_details;
		} else {
			$pay_details = $this->_order;
		}

		$this->controller->set(compact('site_domain','site_name','msg','failure','pay_details'));
		$this->Email->to = $payer_email;
		$this->Email->bcc = $emails;
		$this->Email->from = $site_name.' <noreply@'.$site_domain.'>';
		$this->Email->subject = __('asunto_correo',true);
		$this->Email->sendAs = 'html';
		$this->Email->template = 'payment';
		
		//$this->Email->delivery = Configure::read('debug') ? 'debug':'mail';
		$this->Email->delivery = 'mail';
		$result = $this->Email->send();
		return $result;
	}

	function beforeRender(&$controller){
		if($this->Session->check('cart.flash')){
			$controller->set('cart_flash',$this->Session->read('cart.flash'));
			$this->Session->delete('cart.flash');
		}
		if($this->Session->check('cart.out_of_stock')){
			$controller->set('out_of_stock',$this->Session->read('cart.out_of_stock'));
			$this->Session->delete('cart.out_of_stock');
		}
		//if($this->guest){ $this->Cookie->write('cart',$this->Session->read('cart')); }
	}
}
?>