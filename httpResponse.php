<?php
/*****************************************************************
 * These functions are called to return http response codes      *
 * Use them to handle errors in your code cleanly                *
 *****************************************************************/

//Return 202 and optionally reply with a json object
function resourceCreate($obj) {
	http_response_code(201);
	echo json_encode($obj);
	die();
}

function resourceNotFound() {
	http_response_code(404);
	die("not found"); //TODO remove string. Only for debugging purposes.
}

function internalServerError($msg) {
	http_response_code(500);
	die($msg);
}

function malformedRequest() {
	http_response_code(400);
	die("malformed"); //TODO remove string. Only for debugging.
}
