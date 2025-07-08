<?php

namespace Garavel\Http;

class ServerVariables
{
	/**
	 * Server variables.
	 * 
	 * @var array
	 */
	public array $data = [];

	/**
	 * Constructs a new ServerVariables instance.
	 * 
	 * If $data is null, the global $_SERVER array is used.
	 * 
	 * @param ?array $data The server variables.
	 */
	public function __construct( ?array $data = null )
	{
		$this->data = $data ?? $_SERVER;
	}

	/**
	 * Retrieves a server variable by key.
	 * 
	 * @param string $key The server variable key to retrieve.
	 * @return mixed The value of the server variable, or null if not set.
	 */
	public function get( string $key ): mixed
	{
		return array_key_exists( $key, $this->data )
			? $this->data[ $key ]
			: null;
	}
}
