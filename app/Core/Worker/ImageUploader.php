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
		$this->flysystem->put($data['cloudPath'] . $data['filename'], $data['default']);
	    $this->flysystem->put($data['cloudPath'] . "large_" . $data['filename'], $data['large']);
	    $this->flysystem->put($data['cloudPath'] . "medium_" . $data['filename'], $data['medium']);
	    $this->flysystem->put($data['cloudPath'] . "cart_" . $data['filename'], $data['cart']);
	}
}