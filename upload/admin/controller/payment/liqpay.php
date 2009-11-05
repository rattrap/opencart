<?php 
class ControllerPaymentLiqPay extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/liqpay');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('liqpay', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->https('extension/payment'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_liqpay'] = $this->language->get('text_liqpay');
		$this->data['text_card'] = $this->language->get('text_card');
		
		$this->data['entry_merchant'] = $this->language->get('entry_merchant');
		$this->data['entry_signature'] = $this->language->get('entry_signature');
		$this->data['entry_type'] = $this->language->get('entry_type');				
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning'])) { 
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) { 
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}
		
		if (isset($this->error['signature'])) { 
			$this->data['error_signature'] = $this->error['signature'];
		} else {
			$this->data['error_signature'] = '';
		}
		
		if (isset($this->error['type'])) { 
			$this->data['error_type'] = $this->error['type'];
		} else {
			$this->data['error_type'] = '';
		}

		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => $this->url->https('common/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => $this->url->https('extension/payment'),
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => $this->url->https('payment/liqpay'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->https('payment/liqpay');
		
		$this->data['cancel'] = $this->url->https('extension/payment');
		
		if (isset($this->request->post['liqpay_merchant'])) {
			$this->data['liqpay_merchant'] = $this->request->post['liqpay_merchant'];
		} else {
			$this->data['liqpay_merchant'] = $this->config->get('liqpay_merchant');
		}

		if (isset($this->request->post['liqpay_signature'])) {
			$this->data['liqpay_signature'] = $this->request->post['liqpay_signature'];
		} else {
			$this->data['liqpay_signature'] = $this->config->get('liqpay_signature');
		}

		if (isset($this->request->post['liqpay_type'])) {
			$this->data['liqpay_type'] = $this->request->post['liqpay_type'];
		} else {
			$this->data['liqpay_type'] = $this->config->get('liqpay_type');
		}
		
		if (isset($this->request->post['liqpay_order_status_id'])) {
			$this->data['liqpay_order_status_id'] = $this->request->post['liqpay_order_status_id'];
		} else {
			$this->data['liqpay_order_status_id'] = $this->config->get('liqpay_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['liqpay_geo_zone_id'])) {
			$this->data['liqpay_geo_zone_id'] = $this->request->post['liqpay_geo_zone_id'];
		} else {
			$this->data['liqpay_geo_zone_id'] = $this->config->get('liqpay_geo_zone_id'); 
		} 		
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['liqpay_status'])) {
			$this->data['liqpay_status'] = $this->request->post['liqpay_status'];
		} else {
			$this->data['liqpay_status'] = $this->config->get('liqpay_status');
		}
		
		if (isset($this->request->post['liqpay_sort_order'])) {
			$this->data['liqpay_sort_order'] = $this->request->post['liqpay_sort_order'];
		} else {
			$this->data['liqpay_sort_order'] = $this->config->get('liqpay_sort_order');
		}
		
		$this->template = 'payment/liqpay.tpl';
		$this->children = array(
			'common/header',	
			'common/footer',	
			'common/menu'	
		);
		
		$this->response->setOutput($this->render(TRUE));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/liqpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['liqpay_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['liqpay_signature']) {
			$this->error['signature'] = $this->language->get('error_signature');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>