<?php
namespace freedimension\rest;

/**
 * Lightweight REST API.
 * @package freedimension\rest
 */
class rest
{
	/**
	 * @var string Static part of the REST-URIs to be called.
	 */
	protected $sBaseUri = "";
	/**
	 * @var null|resource CURL resource to be used within the class instance.
	 */
	protected $rCurl = null;
	/**
	 * @var array Holds the options set with curl_setopt, for easy retrieval.
	 */
	protected $hOption = [];

	/**
	 * Constructor of the class instance.
	 *
	 * @param string $sBaseUri Static beginning of all URIs to be called within the created instance.
	 */
	public function __construct ($sBaseUri = "")
	{
		$this->sBaseUri = rtrim($sBaseUri, "/");
		$this->rCurl = curl_init();
	}

	/**
	 * Posts a HTTP DELETE request.
	 *
	 * @param string $sPath Dynamic part of the URI that's to be called.
	 * @param string $sData Optional data part to be sent with the request.
	 * @param bool $bDecode Decodes JSON if true, otherwise returns the response untouched.
	 * @return mixed Response data.
	 */
	public function delete (
		$sPath,
		$sData = "",
		$bDecode = false
	){
		$this->init($sPath);
		$this->opt(CURLOPT_CUSTOMREQUEST, "DELETE");
		$this->opt(CURLOPT_POSTFIELDS, $sData);
		$this->opt(CURLOPT_RETURNTRANSFER, true);
		return $this->exec($bDecode);
	}

	/**
	 * Posts a HTTP GET request.
	 *
	 * @param string $sPath Dynamic part of the URI that's to be called.
	 * @param bool $bDecode Decodes JSON if true, otherwise returns the response untouched.
	 * @return mixed Response data. JSON is already decoded.
	 */
	public function get (
		$sPath,
		$bDecode = false
	){
		$this->init($sPath);
		$this->opt(CURLOPT_RETURNTRANSFER, 1);
		return $this->exec($bDecode);
	}

	/**
	 * Sets or retrieves a CURL option.
	 *
	 * @param integer $iOption One of the CURL options (see curl_setopt!).
	 * @param null $mValue Value the option is to be set. If this is null the value is to be retrieved.
	 * @return bool Returns the current value if $mValue is null, otherwise the return of the curl_setopt method.
	 */
	public function opt (
		$iOption,
		$mValue = null
	){
		$sResult = null;
		if ( null === $mValue )
		{
			$sResult = $this->hOption[$iOption];
		}
		else
		{
			$this->hOption[$iOption] = $mValue;
			$sResult = curl_setopt($this->rCurl, $iOption, $mValue);
		}
		return $sResult;
	}

	/**
	 * Posts a HTTP POST request.
	 *
	 * @param string $sPath Dynamic part of the URI that's to be called.
	 * @param mixed $mData Data to be sent with the POST request (i.e. the body part).
	 * @param bool $bDecode Decodes JSON if true, otherwise returns the response untouched.
	 * @return mixed The response data of the CURL call.
	 */
	public function post (
		$sPath,
		$mData,
		$bDecode = false
	){
		$this->init($sPath);
		$mData = $this->encode($mData);
		$this->opt(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		$this->opt(CURLOPT_POST, 1);
		$this->opt(CURLOPT_POSTFIELDS, $mData);
		$this->opt(CURLOPT_RETURNTRANSFER, true);
		return $this->exec($bDecode);
	}

	/**
	 * Posts a HTTP PUT request.
	 *
	 * @param string $sPath Dynamic part of the URI that's to be called.
	 * @param mixed $mData Data to be sent with the PUT request (i.e. the body part).
	 * @param bool $bDecode Decodes JSON if true, otherwise returns the response untouched.
	 * @return mixed The response data of the CURL call.
	 */
	public function put (
		$sPath,
		$mData,
		$bDecode = false
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
		return $this->exec($bDecode);
	}

	/**
	 * Sets the static beginning of all URIs to be called.
	 *
	 * Typically this is the part of the URI telling where the REST-API has its home (http://www.example.com/dev/api/rest or the like).
	 *
	 * @param string $sBaseUri First (static) part of the URI.
	 */
	public function setBaseUri ($sBaseUri)
	{
		$this->sBaseUri = $sBaseUri;
	}

	/**
	 * Encodes data to JSON if necessary.
	 *
	 * @param mixed $mData Data that possibly needs encoding.
	 * @return string JSON encoded data.
	 */
	protected function encode ($mData)
	{
		if ( is_array($mData) )
		{
			$mData = json_encode($mData);
		}
		return $mData;
	}

	/**
	 * Executes a REST-Call.
	 *
	 * Helper method for front methods like put, post etc.
	 *
	 * @param bool $bDecode Set to true if response data needs to be decoded.
	 * @return mixed Response data of the REST call.
	 */
	protected function exec (
		$bDecode = false
	){
		$mResponse = curl_exec($this->rCurl);
		if ( $bDecode )
		{
			$mResponse = json_decode($mResponse, true);
		}
		return $mResponse;
	}

	/**
	 * Initializes a new REST-Call.
	 *
	 * @param string $sPath Dynamic part of the URI that's to be called.
	 * @return null|resource Handle to the CURL resource.
	 */
	protected function init ($sPath)
	{
		$this->hOption = [];
		curl_reset($this->rCurl);
		$this->opt(CURLOPT_URL, $this->sBaseUri . "/" . ltrim($sPath, "/"));
		return $this->rCurl;
	}
}