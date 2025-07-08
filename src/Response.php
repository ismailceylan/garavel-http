<?php

namespace Garavel\Http;

use Garavel\Support\Arr;
use Garavel\Support\Str;

class Response
{
	/**
	 * Http status code.
	 * 
	 * @var int
	 */
	public int $status = 200;

	/**
	 * Header stack.
	 * 
	 * @var array
	 */
	public array $headers = [];

	/**
	 * Response body.
	 * 
	 * @var array
	 */
	public array $body = [];

	/**
	 * Constructs a new Response instance with default headers for CORS.
	 * 
	 * Initializes the response by setting default headers to allow cross-origin 
	 * requests from any origin and specifying allowed headers for requests.
	 */
	public function __construct()
	{
		$this->header( 'Access-Control-Allow-Origin', '*' );

		$this->header(
			'Access-Control-Allow-Headers',
			'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
		);
	}

	/**
	 * Returns a new Response instance with the given headers.
	 * 
	 * @param array $headers Associative array of headers
	 * @return Response
	 */
	public function withHeaders( array $headers ): Response
	{
		foreach( $headers as $key => $val )
		{
			$this->header( $key, $val );
		}

		return $this;
	}

	/**
	 * Sets a header by key and optional value.
	 * 
	 * If no value is given, the header with the given key will be removed.
	 * 
	 * @param string $key   The header key
	 * @param mixed  $value The value to set (optional)
	 * @return Response
	 */
	public function header( string $key, mixed $value = null ): Response
	{
		$this->headers[ $key ] = $value;
		return $this;
	}

	/**
	 * Checks if a header exists in the response.
	 *
	 * @param string $key The header key to check for.
	 * @return bool True if the header exists, false otherwise.
	 */
	public function hasHeader( string $key ): bool
	{
		return array_key_exists( $key, $this->headers );
	}

	/**
	 * Retrieves a header by key.
	 * 
	 * @param string $key The header key to retrieve
	 * @return mixed The value of the header, or null if not set.
	 */
	public function getHeader( string $key ): mixed
	{
		return $this->headers[ $key ] ?? null;
	}

	/**
	 * Writes a string to the response body.
	 * 
	 * Appends the given string to the response body and updates the
	 * Content-Length header accordingly.
	 * 
	 * @param string $str The string to write.
	 * @return Response The response instance.
	 */
	public function write( $str ): Response
	{
		$contentLength = 'Content-Length';
		
		$this->header( $contentLength,
			( $this->getHeader( $contentLength ) ?? 0 ) + mb_strlen( $str )
		);

		$this->body[] = $str;

		return $this;
	}

	/**
	 * Flushes the response to the output.
	 * 
	 * Sends the status, headers, and response body to the output.
	 */
	public function flush(): void
	{
		$this->header( "HTTP/1.1 $this->status" );

		foreach( $this->headers as $key => $val )
		{
			header( $key . Str::prefix( ': ', $val ));
		}

		echo Arr::join( $this->body, '' );
	}

	/**
	 * Sets the HTTP status code for the response.
	 * 
	 * @param int $status The HTTP status code to set.
	 * @return Response The response instance with the updated status.
	 */
	public function status( int $status ): Response
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * Sets the response body as JSON and sets the HTTP status code.
	 * 
	 * @param mixed $data The data to encode as JSON. If not given, the current response body is used.
	 * @param int $status The HTTP status code to set.
	 * @return JsonResponse The response instance with the updated body and status.
	 */
	public function json( mixed $data = null, int $status = 200 ): JsonResponse
	{
		return new JsonResponse( $data, $status );
	}

}
