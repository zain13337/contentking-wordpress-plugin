<?php

class ContentkingAPI implements ContentkingAPIInterface {

	protected $api_url = 'https://api.contentkingapp.com/v1/';
	//protected $api_url = 'https://staging-api.contentking.io/v1/';
	/*
	* Performs api call to validate token.
	*
	* @param string $token API secret token to be validated.
	* @return Bool
	*/
	public function check_token( $token = '' ){

		if( $token === '' ):
			$token = get_option( 'contentking_client_token' );
		endif;

		$data = $this->prepare_request_data( ['token' => $token] );
		$loger = new LoggerFile();
		$response = wp_remote_post( $this->api_url . 'check_token', $data );
		$loger->log( print_r($data,true) );
		$loger->log( print_r($response,true) );
		if ( is_wp_error( $response ) ):
			return false;

		elseif( isset( $response['response']['code'] ) && intval( $response['response']['code'] ) === 200 ):
			return true;
		endif;

		return false;
	}

	/*
	* Performs api call to send URL to Contentking.
	*
	* @param string $url URL to be sent to Contentking.
	* @return Bool
	*/
	public function check_url( $url = '' ){

		$data = $this->prepare_request_data( ['url'=> $url] );
		$response = wp_remote_post( $this->api_url . 'check_url', $data );
		$logger = new LoggerFile();
		$logger->log(print_r($response, true));
		if ( !is_wp_error( $response ) ):
			if( isset( $response['response']['code'] ) ):
				if(intval( $response['response']['code'] ) === 200):
					return true;
				elseif( intval( $response['response']['code'] ) === 401 ):
					if( isset($response['body']['code']) && $response['body']['code'] === 'auth_failed' ):
						return false;
					endif;
				endif;
			endif;

		endif;

		return true;

	}

	/*
	* Prepare HTTP request data for API call
	*
	* @param array $data input data.
	* @return Array HTTP request data.
	*/
	private function prepare_request_data( $data = [] ){

		if(empty($data))
			return [];

		if( isset( $data['token'] ) ):
			$token = 	$data['token'];
		else:
			$token = get_option( 'contentking_client_token' );
		endif;

		$prepared_data = [
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => 'token ' . $token
			]
		];
		$prepared_data['body'] = json_encode([]);
		if( isset( $data['url'] ) ):
			$prepared_data['body'] = json_encode(['url' => $data['url']]);
		endif;

		return $prepared_data;

	}



}
