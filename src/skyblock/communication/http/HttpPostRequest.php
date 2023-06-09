<?php

declare(strict_types=1);

namespace skyblock\communication\http;

class HttpPostRequest {

	private string $url;
	private array $data;

	private string $baseURL = "http://135.148.150.31:8088/";
	//private string $baseURL = "http://localhost:8088/";

	public function __construct(string $url, array $data){
		$this->url = $url;
		$this->data = $data;
	}

	public function execute(): array {
		$ch = curl_init($this->baseURL . $this->url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);


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

	public function setBaseURL(string $baseURL) : void{
		$this->baseURL = $baseURL;
	}


}