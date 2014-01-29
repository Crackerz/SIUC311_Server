<?php 

//Setup our REST framework library
//github.com/jmathai/epiphany
include_once './epiphany/src/Epi.php';
Epi::setPath('base','./epiphany/src');
Epi::init('route');

//Load in our API configuration file
Epi::setPath('config','./');
getRoute()->load('api.ini');

//Execute our API handlers for the current request
getRoute()->run();

class Ticket {
	public static function addNew() {
		echo "adding ticket!";
	}

	public static function fetchExisting() {
		echo "getting ticket!";

	}

	public static function deleteExisting() {
		echo "deleting ticket!";	
	}

	public static function updateExisting() {
		echo "updating ticket!";
	}
}

function notImplemented() {
	http_response_code(404);
	echo "This is a server. It should not be being viewed by a web browser. The requested REST API call you issued is not implemented here. Please re-evaluate the life decisions that lead you to this message.";
}

