<?php namespace App\Core\Worker;

class ImageUploader
{
	public function fire($job, $data)
	{
		\Flysystem::put($data['cloudPath'] . $data['filename'], $data['default']);
	    // \Flysystem::put($data['cloudPath'] . "large_" . $data['filename'], $data['large']);
	    // \Flysystem::put($data['cloudPath'] . "medium_" . $data['filename'], $data['medium']);
	    // \Flysystem::put($data['cloudPath'] . "cart_" . $data['filename'], $data['cart']);
	}
}