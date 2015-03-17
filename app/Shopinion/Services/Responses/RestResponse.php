<?php namespace Shopinion\Services\Responses;

class RestResponse
{
	public static function Response($code, $data = null, $to_array = true)
	{
		switch ($code) {
			case 100 :
				$message = 'Continue';
				break;
			case 101 :
				$message = 'Switching Protocols';
				break;
			case 200 :
				$message = 'OK';
				break;
			case 201 :
				$message = 'Created';
				break;
			case 202 :
				$message = 'Accepted';
				break;
			case 203 :
				$message = 'Non-Authoritative Information';
				break;
			case 204 :
				$message = 'No Content';
				break;
			case 205:
				$message = 'Reset Content';
				break;
			case 206 :
				$message = 'Partial Content';
				break;
			case 300 :
				$message = 'Multiple Choice';
				break;
			case 301 :
				$message = 'Moved Permanently';
				break;
			case 302 :
				$message = 'Found';
				break;
			case 303 :
				$message = 'See Other';
				break;
			case 304 :
				$message = 'Not Modified';
				break;
			case 305 :
				$message = 'Use Proxy';
				break;
			case 307 :
				$message = 'Temporary Redirect';
				break;
			case 400 :
				$message = 'Bad Request';
				break;
		}

		$data = ($to_array) ? $data->toArray() : $data;

		$response = array(
			'status_code' => $code,
			'message'	  => $message,
			'data'        => $data
		);

		return $response;
	}
}