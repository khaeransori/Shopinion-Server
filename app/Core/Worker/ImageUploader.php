<?php namespace App\Core\Worker;

use GrahamCampbell\Flysystem\FlysystemManager;

class ImageUploader
{
	protected $flysystem;
	function __construct(FlysystemManager $flysystem) {
		$this->flysystem = $flysystem;
	}

	public function fire($job, $data)
	{
		$this->flysystem->put($data['cloudPath'] . $data['filename'], (string) $data['default']);
	    $this->flysystem->put($data['cloudPath'] . "large_" . $data['filename'], (string) $data['large']);
	    $this->flysystem->put($data['cloudPath'] . "medium_" . $data['filename'], (string) $data['medium']);
	    $this->flysystem->put($data['cloudPath'] . "cart_" . $data['filename'], (string) $data['cart']);
	}
}