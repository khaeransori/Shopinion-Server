<?php

Event::listen('tymon.jwt.absent', function ()
{
	$response = array(
		'status_code' => 400,
		'message' => 'Token not provided'
	);
	return Response::json($response, 400);
});

Event::listen('tymon.jwt.expired', function ()
{
	$response = array(
		'status_code' => 401,
		'message' => 'Token expired'
	);
	return Response::json($response, 401);
});

Event::listen('tymon.jwt.absent', function ()
{
	$response = array(
		'status_code' => 401,
		'message' => 'Token invalid'
	);
	return Response::json($response, 401);
});

Event::listen('tymon.jwt.absent', function ()
{
	$response = array(
		'status_code' => 404,
		'message' => 'User not found'
	);
	return Response::json($response, 404);
});