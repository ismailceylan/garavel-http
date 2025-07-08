<?php

namespace Garavel\Http;

/**
 * Represents an HTTP request.
 */
class Request
{
	/**
	 * POST, GET and Input datas.
	 * 
	 * @var array
	 */
	public array $data;

	/**
	 * Server variables.
	 * 
	 * @var ServerVariables
	 */
	public ServerVariables $server;

	/**
	 * Constructor.
	 *
	 * Merges the $_POST and $_GET variables together. Also adds the
	 * contents of php://input as an object to the array (if it exists).
	 * The 'server' member is also initialized.
	 */
	public function __construct()
	{
		$_INPUT = json_decode(
			file_get_contents( 'php://input' )
		);

		$this->data = array_merge( $_POST, $_GET, (array) $_INPUT );
		$this->server = new ServerVariables;
	}

	/**
	 * Magic getter to access input data.
	 *
	 * Retrieves the value associated with the specified key from the input data.
	 *
	 * @param string $key The key to retrieve from the input data.
	 * @return mixed The value associated with the key, or null if not found.
	 */
	public function __get( string $key ): mixed
	{
		return $this->input( $key );
	}

	/**
	 * Retrieves the value associated with the specified key from the input data.
	 *
	 * If $key is null, the entire input data is returned.
	 *
	 * @param string|null $key The key to retrieve from the input data.
	 * @param mixed $default The default value to return if the key is not found.
	 * @return mixed The value associated with the key, or $default if not found.
	 */
	public function input( ?string $key = null, mixed $default = null ): mixed
	{
		return $key === null
			? $this->data
			: $this->data[ $key ] ?? $default;
	}

	/**
	 * Retrieves the path of the current request.
	 *
	 * The path is the relative path of the current request, without the
	 * directory name of the current script. If the `REDIRECT_URL` server
	 * variable is not set, an empty string is returned.
	 *
	 * @return string The path of the current request.
	 */
	public function path(): string
	{
		return str_replace(
			pathinfo( $this->server->get( 'SCRIPT_NAME' ))[ 'dirname' ] . '/',
			'',
			$this->server->get( 'REDIRECT_URL' ) ?? ''
		);
	}

	/**
	 * Retrieves the HTTP method of the current request.
	 *
	 * Converts the request method to uppercase for consistency.
	 *
	 * @return string The HTTP method of the request (e.g., 'GET', 'POST').
	 */
	public function method(): string
	{
		return strtoupper(
			$this->server->get( 'REQUEST_METHOD' )
		);
	}

	/**
	 * Determines if the current request is an AJAX request.
	 *
	 * Checks if the 'HTTP_X_REQUESTED_WITH' server variable is set to
	 * 'XmlHttpRequest', indicating that the request was made via AJAX.
	 *
	 * @return bool True if the request is an AJAX request, false otherwise.
	 */
	public function ajax(): bool
	{
		return $this->server->get( 'HTTP_X_REQUESTED_WITH' ) == 'XmlHttpRequest';
	}
}
