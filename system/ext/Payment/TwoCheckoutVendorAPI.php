<?php

if (!defined('CURLOPT_PROTOCOLS')) define ('CURLOPT_PROTOCOLS', 181);
if (!defined('CURLPROTO_HTTPS')) define ('CURLPROTO_HTTPS', 2);

class TwoCheckoutVendorAPI {
	var $url;
	var $user;
	var $pass;	
	
	function TwoCheckoutVendorAPI($url, $user, $pass) {
		$this->url = $url;
		$this->user = $user;
		$this->pass = $pass;
	}

    function call($url_suffix, $data=array()) {
        $ch = curl_init($this->url . $url_suffix);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->user}:{$this->pass}");
        if(count($data) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $resp = curl_exec($ch);
        curl_close($ch);

        $responseObject = json_decode($resp);
        if(isset($responseObject->errors)){
            $return_message = $responseObject->errors[0]->code.': '.$responseObject->errors[0]->message;
            return array('success' => false, 'response' => $return_message);
        }
        else {
            $return_message = $responseObject;
            if(isset($responseObject->responseMessage)){
                $return_message = $responseObject->responseMessage;
            }
            else if(isset($responseObject->message)){
                $return_message = $responseObject->message;
            }

            return array('success' => true, 'response' => $return_message);
        }
	}

	function detailCompanyInfo() {
		$url_suffix = 'acct/detail_company_info';
		return $this->call($url_suffix);
	}

	function detailContactInfo() {
		$url_suffix = 'acct/detail_contact_info';
		return $this->call($url_suffix);
	}

	function listSales($args) {
		$url_suffix ='sales/list_sales';
        return $this->call($url_suffix, $args);
	}

	function detailSale($args) {
		$url_suffix ='sales/detail_sale';
        return $this->call($url_suffix, $args);
	}

	function markShipped($args) {
		$url_suffix ='sales/mark_shipped';
        return $this->call($url_suffix, $args);
	}

	function reauth($args) {
		$url_suffix ='sales/reauth';
        return $this->call($url_suffix, $args);
	}

	function refundInvoice($args) {
		$url_suffix ='sales/refund_invoice';
        return $this->call($url_suffix, $args);
	}

	function refundLineitem($args) {
		$url_suffix ='sales/refund_lineitem';
        return $this->call($url_suffix, $args);
	}

	function stopLineitemRecurring($args) {
		$url_suffix ='sales/stop_lineitem_recurring';
        return $this->call($url_suffix, $args);
	}

	function createComment($args) {
		$url_suffix ='sales/create_comment';
        return $this->call($url_suffix, $args);
	}

	function createCoupon($args) {
		$url_suffix ='products/create_coupon';
        return $this->call($url_suffix, $args);
	}

	function updateCoupon($args) {
		$url_suffix ='products/update_coupon';
        return $this->call($url_suffix, $args);
	}

	function listCoupons($args) {
		$url_suffix ='products/list_coupons';
        return $this->call($url_suffix, $args);
	}

	function detailCoupon($args) {
		$url_suffix ='products/detail_coupon';
        return $this->call($url_suffix, $args);
	}

	function deleteCoupon($args) {
		$url_suffix ='products/delete_coupon';
        return $this->call($url_suffix, $args);
	}

	function createOption($args) {
		$url_suffix ='products/create_option';
        return $this->call($url_suffix, $args);
	}

	function updateOption($args) {
		$url_suffix ='products/update_option';
        return $this->call($url_suffix, $args);
	}

	function listOptions($args) {
		$url_suffix ='products/list_options';
        return $this->call($url_suffix, $args);
	}

	function detailOption($args) {
		$url_suffix ='products/detail_option';
        return $this->call($url_suffix, $args);
	}

	function deleteOption($args) {
		$url_suffix ='products/delete_option';
        return $this->call($url_suffix, $args);
	}

	function createProduct($args) {
		$url_suffix ='products/create_product';
        return $this->call($url_suffix, $args);
	}

	function updateProduct($args) {
		$url_suffix ='products/update_product';
        return $this->call($url_suffix, $args);
	}

	function listProducts($args) {
		$url_suffix ='products/list_products';
        return $this->call($url_suffix, $args);
	}

	function detailProduct($args) {
		$url_suffix ='products/detail_product';
        return $this->call($url_suffix, $args);
	}

	function deleteProduct($args) {
		$url_suffix ='products/delete_product';
        $this->call($url_suffix, $args);
	}
}