<?php
namespace freedimension\rest;

class rest
{
	protected $sBaseUri = "";
	protected $rCurl    = null;
	protected $hOption  = [];

	public function __construct ($sBaseUri = "")
	{
		$this->sBaseUri = rtrim($sBaseUri, "/");
		$this->rCurl = curl_init();
	}

	public function delete (
		$sPath,
		$sData = ""
	){
		$this->init($sPath);
		$this->opt(CURLOPT_CUSTOMREQUEST, "DELETE");
		$this->opt(CURLOPT_POSTFIELDS, $sData);
		$this->opt(CURLOPT_RETURNTRANSFER, true);
		return $this->exec();
	}

	public function get ($sPath = null)
	{
		$this->init($sPath);
		$this->opt(CURLOPT_RETURNTRANSFER, 1);
		var_dump($this);
		return $this->exec(true);
	}

	public function opt (
		$iOption,
		$mValue = null
	){
		if ( null === $mValue )
		{
			return $this->hOption[$iOption];
		}
		else
		{
			$this->hOption[$iOption] = $mValue;
			return curl_setopt($this->rCurl, $iOption, $mValue);
		}
	}

	public function post (
		$sPath,
		$mData
	){
		$this->init($sPath);
		$mData = $this->encode($mData);
		$this->opt(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		$this->opt(CURLOPT_POST, 1);
		$this->opt(CURLOPT_POSTFIELDS, $mData);
		$this->opt(CURLOPT_RETURNTRANSFER, true);
		return $this->exec();
	}

	public function put (
		$sPath,
		$mData
	){
		$this->init($sPath);
		$mData = $this->encode($mData);
		$this->opt(CURLOPT_HTTPHEADER,
			[
				'Content-Type: application/json',
				'Content-Length: ' . strlen($mData)
			]
		);
		$this->opt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$this->opt(CURLOPT_POSTFIELDS, $mData);
		$this->opt(CURLOPT_RETURNTRANSFER, true);
		return $this->exec();
	}

	public function setBaseUri ($sBaseUri)
	{
		$this->sBaseUri = $sBaseUri;
	}

	protected function encode ($mData)
	{
		if ( is_array($mData) )
		{
			$mData = json_encode($mData);
		}
		return $mData;
	}

	protected function exec (
		$bDecode = false
	){
		$mResponse = curl_exec($this->rCurl);
		if ( $bDecode )
		{
			$mResponse = json_decode($mResponse);
		}
		return $mResponse;
	}

	protected function init ($sPath)
	{
		$this->hOption = [];
		curl_reset($this->rCurl);
		$this->opt(CURLOPT_URL, $this->sBaseUri . "/" . ltrim($sPath, "/"));
		return $this->rCurl;
	}
}