<?php namespace App\Core\Worker;

class ImageUploader
{
	public function fire($job, $data)
	{
		$default = (string) \Image::make($data['default'])->encode('png');
		$large = (string) \Image::make($data['large'])->encode('png');
		$medium = (string) \Image::make($data['medium'])->encode('png');
		$cart = (string) \Image::make($data['cart'])->encode('png');

		\Flysystem::put($data['cloudPath'] . $data['filename'], $default);
	    \Flysystem::put($data['cloudPath'] . "large_" . $data['filename'], $large);
	    \Flysystem::put($data['cloudPath'] . "medium_" . $data['filename'], $medium);
	    \Flysystem::put($data['cloudPath'] . "cart_" . $data['filename'], $cart);
	}
}