<?php 
	namespace Dingtalk\Api;
	
	use Dingtalk\Utils\Http;
	use Dingtalk\Utils\Cache;

	class Get
	{
  		private static $appConfig = [];

		public function __construct($config)
	    {
	        self::$appConfig = $config;
	    }
	    /**
	     * [getAccessToken description]
	     * @return [type] [description]
	     */
	    public function getAccessToken()
	    {
	    	 $accessToken = Cache::get('GLOBAL_access_token');

	    }


	}