<?php

class ProductImagesController extends \BaseController {

	function __construct(ProductImage $repo, Product $product, REST $rest) {
		$this->product = $product;
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of the resource.
	 * GET /productimages
	 *
	 * @return Response
	 */
	public function index()
	{
		$per_product = Input::get('per_product', 0);
		$product_id	 = Input::get('product_id');

		$images = $this->repo;

		if ($per_product !== 0) {
			$this->product->findOrFail($product_id);
			$images->where('product_id', $product_id);
		}

		return $this->rest->response(200, $images->get());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /productimages
	 *
	 * @return Response
	 */
	public function store()
	{
		$product_id = Input::get('product_id');

		$this->product->findOrFail($product_id);

		$image = new $this->repo;
		if ($image->save()) {
			$path = public_path() . '/images/product/' . $product_id;
			$image_path = $path . '/' . $image->id;

			if (!File::isDirectory($path)) {
				File::makeDirectory($path);
			}

			if (File::makeDirectory($image_path)) {
				
				// ambil image defaultnya
				$img = Image::make(Input::file('file'));
				
				// buat image default ukuran tingginya 800px lebar sesuai rasio
				$img->resize(null, 800, function($constraint)
				{
					$constraint->aspectRatio();
				})->save($image_path. '/' . $image->id . '.jpg');

				// save ukuran 458
				$img = Image::make(Input::file('file'));
				$img->resize(null, 458, function($constraint)
				{
					$constraint->aspectRatio();
				})->save($image_path. '/' . $image->id . '-large_default.jpg');

				// save ukuran 125
				$img = Image::make(Input::file('file'));
				$img->resize(null, 125, function($constraint)
				{
					$constraint->aspectRatio();
				})->save($image_path. '/' . $image->id . '-medium_default.jpg');
				
				// save ukuran 98
				$img = Image::make(Input::file('file'));
				$img->resize(null, 98, function($constraint)
				{
					$constraint->aspectRatio();
				})->save($image_path. '/' . $image->id . '-small_default.jpg');

				// save ukuran 80
				$img = Image::make(Input::file('file'));
				$img->resize(null, 80, function($constraint)
				{
					$constraint->aspectRatio();
				})->save($image_path. '/' . $image->id . '-cart_default.jpg');

				return $this->rest->response(201, $image);
			}
		}
		
		return $this->response->errorBadRequest($image->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /productimages/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$image = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			// $path = public_path() . '/images/product/' . $image->product_id;
			// $image_path = $path . '/' . $image->id;

			// File::deleteDirectory($image_path, true);
			return $this->rest->response(202, $image);
		}

		return $this->response->errorBadRequest();
	}

}