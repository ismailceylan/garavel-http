<?php

namespace Garavel\Http;

/**
 * Represents a json response.
 */
class JsonResponse extends Response
{
	/**
	 * Json data source.
	 * 
	 * @var array
	 */
	public array $data = [];

	/**
	 * Constructs a new JsonResponse instance.
	 * 
	 * @param array $data The data that will be converted to JSON and written to the response body.
	 */
	public function __construct( ?array $data = null )
	{
		parent::__construct();
		
		$this->header( 'Content-Type', 'application/json' );

		if( $data )
		{
			$this->write( $data );
		}
	}

	/**
	 * Appends data to the response.
	 *
	 * If the data is an array or object, it will be merged with the existing data.
	 * If the data is a string, integer, or boolean, it will be added to the existing data
	 * as a new element.
	 *
	 * @param array|object|string|int|bool $data The data to append.
	 * @return static
	 */
	public function write( $data ): JsonResponse
	{
		if( is_array( $data ) || is_object( $data ))
		{
			$this->data = array_merge((array) $this->data, (array) $data );
		}
		else if( is_string( $data ) || is_int( $data ) || is_bool( $data ))
		{
			$this->data[] = $data;
		}

		return $this;
	}

	/**
	 * Flushes the response to the output.
	 *
	 * Encodes the data array to json and writes it to the response body,
	 * then calls the parent flush method.
	 */
	public function flush(): void
	{
		parent::write( json_encode( $this->data ));
		parent::flush();
	}

	/**
	 * Sets a successful JSON response with a custom message, status, and additional data.
	 *
	 * @param string $message The success message to be included in the response.
	 * @param int $status The HTTP status code for the response. Defaults to 200.
	 * @param array $extend Additional data to be merged into the response.
	 * @return JsonResponse The JsonResponse instance with the success status set.
	 */
	public function success(
		string $message = 'Successful.',
		int $status = 200,
		array $extend = []
	): JsonResponse
	{
		return $this->set( 'success', $message, $status, $extend );
	}

	/**
	 * Sets a failed JSON response with a custom message, status, and additional data.
	 *
	 * @param string $message The failure message to be included in the response.
	 * @param int $status The HTTP status code for the response. Defaults to 500.
	 * @param array $extend Additional data to be merged into the response.
	 * @return JsonResponse The JsonResponse instance with the failed status set.
	 */
	public function fail(
		string $message = 'Failed.',
		int $status = 500,
		array $extend = []
	): JsonResponse
	{
		return $this->set( 'failed', $message, $status, $extend );
	}

	/**
	 * Sets a not found JSON response with a custom message and additional data.
	 *
	 * @param string $message The not found message to be included in the response.
	 * @param array $extend Additional data to be merged into the response.
	 * @return JsonResponse The JsonResponse instance with the not found status set.
	 */
	public function notFound(
		string $message = 'Not found.',
		array $extend = []
	): JsonResponse
	{
		return $this->set( 'not-found', $message, 404, $extend );
	}

	/**
	 * Sets the JSON response data with a status, message, status code, and additional properties.
	 *
	 * @param string $statusName The status name to be included in the response data.
	 * @param string $msg The message to be included in the response data.
	 * @param int $statusCode The HTTP status code for the response.
	 * @param array $props Additional properties to be merged into the response data.
	 * @return JsonResponse The JsonResponse instance with the updated data and status.
	 */
	public function set(
		string $statusName,
		string $msg,
		int $statusCode,
		array $props
	): JsonResponse
	{
		$this->status = $statusCode;

		$this->data = array_merge(
			[
				'status' => $statusName,
				'message' => $msg
			],
			$props
		);

		return $this;
	}

}
