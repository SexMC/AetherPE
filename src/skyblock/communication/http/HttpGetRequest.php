<?php

declare(strict_types=1);

namespace skyblock\communication\http;

class HttpGetRequest {

	private string $url;
	private array $data;

	private string $baseURL = "http://135.148.150.31:8088/";
	//private string $baseURL = "http://localhost:8088/";

	public function __construct(string $url, array $data = []){
		$this->url = $url;
		$this->data = $data;
	}

	public function execute(): array {
		$ch = curl_init($this->baseURL . $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);

		if (curl_error($ch)) {
			trigger_error('Curl Error:' . curl_error($ch));
		}

		$response = curl_exec($ch);
		curl_close($ch);

		if(is_string($response)){
			//var_dump($response);
			return json_decode($response, true) ?? [];

		}

		return [];
	}

	/**
	 * @param string $baseURL
	 */
	public function setBaseURL(string $baseURL) : void{
		$this->baseURL = $baseURL;
	}

	/**
	 * @return string
	 */
	public function getBaseURL() : string{
		return $this->baseURL;
	}


}