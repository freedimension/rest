<?php
namespace freedimension\rest;

class rest
{
	protected $sBaseUri = "";

	public function __construct ($sBaseUri = "")
	{
		$this->sBaseUri = rtrim($sBaseUri, "/");
	}

	public function delete (
		$sData = "",
		$sPath
	){
		$rCurl = $this->init($sPath);
		curl_setopt($rCurl, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($rCurl, CURLOPT_POSTFIELDS, $sData);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, true);
		return $this->exec($rCurl);
	}

	public function get ($sPath = null)
	{
		$rCurl = $this->init($sPath);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
		return $this->exec($rCurl, true);
	}

	public function post (
		$sData,
		$sPath
	){
		$rCurl = $this->init($sPath);
		curl_setopt($rCurl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($rCurl, CURLOPT_POST, 1);
		curl_setopt($rCurl, CURLOPT_POSTFIELDS, $sData);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, true);
		return $this->exec($rCurl);
	}

	public function put (
		$sData,
		$sPath
	){
		$rCurl = $this->init($sPath);
		curl_setopt($rCurl,
			CURLOPT_HTTPHEADER,
			[
				'Content-Type: application/json',
				'Content-Length: ' . strlen($sData)
			]
		);
		curl_setopt($rCurl, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($rCurl, CURLOPT_POSTFIELDS, $sData);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
		return $this->exec($rCurl);
	}

	public function setBaseUri ($sBaseUri)
	{
		$this->sBaseUri = $sBaseUri;
	}

	protected function exec (
		$rCurl,
		$bDecode = false
	){
		$mResponse = curl_exec($rCurl);
		curl_close($rCurl);
		if ( $bDecode )
		{
			$mResponse = json_decode($mResponse);
		}
		return $mResponse;
	}

	protected function init ($sPath)
	{
		return curl_init($this->sBaseUri . "/" . ltrim($sPath, "/"));
	}
}