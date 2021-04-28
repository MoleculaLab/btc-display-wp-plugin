<?php
/**
 * @package BTCDisplay
 * @version 1.0.0
 */
/*
Plugin Name: BTC Display
Plugin URI: https://moleculalab.com
Description: This plugin for wordpress is used to fetch BTC price from coinbase API  and display in real time using shortcode in front-end.
Shortcodes: 
Only price [btcdisplay]
Incremented Price (operator and value in %) [btcdisplay inc 10]
Decremented Price (operator and value in %) [btcdisplay dec 10]
Author: Molecula-LAB
Version: 1.0.0
Author URI: https://moleculalab.com
*/


Class BTCDisplay{
	
	/**
	*	coinbase website url variable
	*/
	var $url = 'https://api.coinbase.com/v2/prices/spot?currency=COP';
	
	
	function __construct(){
		//add shortcode
		add_shortcode( 'btcdisplay', array($this,'btcdisplay_func') );
		
		//add plugin js
		add_action('wp_enqueue_scripts', array($this,'btcdisplay_init') );
		
		//ajax function to update price
		add_action('wp_ajax_update_price', array($this, 'update_price') );
		add_action('wp_ajax_nopriv_update_price',  array($this,'update_price') );
	}
	
	function btcdisplay_init(){
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'btcdisplay-js', plugins_url( '/js/btcdisplay.js', __FILE__ ));
		wp_localize_script( 'btcdisplay-js', 'btcdisplay', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );		
	}
	
	function btcdisplay_func( $atts ) {
		//operator inc or dec
		$operator = $atts[0];
		
		//value in numeric format
		$value = $atts[1];
		
		
		$amount = $this->getdata();
		
		if(isset($operator) && $operator == 'inc' && $value){
			$amount	=	$amount + ($amount*$value/100);
		}elseif(isset($operator) && $operator == 'dec' && $value){
			$amount	=	$amount - ($amount*$value/100);
		}
		
		return $this->format( $amount, $operator, $value );
	}
	
	/**
	 * Fetch data from coinbase api.
	 *
	 * @since	1.0
	 */
	function getdata(){
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		$data = json_decode($server_output);
		return $data->data->amount;
	}
	
	/**
	 * Chnage value in formated amount.
	 *
	 * @since	1.0
	 */
	function format( $amount, $operator, $value, $html = true ){
		if($html){
			return '<span class="btc_price" data-operator="'.$operator.'" data-value="'.$value.'" >$' . number_format($amount,2) . '</span>';
		}else{
			return '$' . number_format($amount,2) . '';
		}
		
	}
	
	/**
	 * Ajax function get price from api and update.
	 *
	 * @since	1.0
	 */
	function update_price(){
		$operator 	= $_REQUEST['operator'];
		$value		= intval( $_REQUEST['value'] );
		
		$amount = $this->getdata();
		
		if(isset($operator) && $operator == 'inc' && $value){
			$amount	=	$amount + ($amount*$value/100);
		}elseif(isset($operator) && $operator == 'dec' && $value){
			$amount	=	$amount - ($amount*$value/100);
		}
		
		echo $this->format( $amount, $operator, $value, false );
		wp_die();
	}
}
new BTCDisplay();
