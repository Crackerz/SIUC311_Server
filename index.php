<?php 

//Import our own dependencies
include_once './database.php';
include_once './ticket.php';
include_once './httpResponse.php';

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

//The ticketapi class represents our API functionality
class TicketAPI {
	public static function addNew() {
		$ticket = Ticket::ticketFromJson(json_decode(file_get_contents('php://input'))); 
		$sql = new SQLServer();
		if(!$sql->connect()) 
				internalServerError($sql->getError());
		$success = $sql->insertTicket($ticket);
		if($success)
			echo '{id:' . $sql->connection->insert_id . '}';
		else {
			internalServerError($sql->getError());
		}
	}

	public static function fetchExisting() {
		$sql = new SQLServer();
		if(!$sql->connect()) 
				internalServerError($sql->getError());
		echo json_encode($sql->getTicket(getID()));
	}

	public static function deleteExisting() {
		$sql = new SQLServer();
		if(!$sql->connect()) 
				internalServerError($sql->getError());
		if(!$sql->deleteTicket(getId())) {
			resourceNotFound();
		}
	}

	public static function updateExisting() {
		$ticket = Ticket::ticketFromJson(json_decode(file_get_contents('php://input'))); 
		$sql = new SQLServer();
		if(!$sql->connect()) 
			internalServerError($sql->getError());
		if(!$sql->updateTicket(getID(),$ticket))
			resourceNotFound();
	}
}

//This class contains batch queries that allows a client to sync its state with the server
class UpdateAPI {
		public static function updateRecent() {
			$sql = new SQLServer();
			if(!$sql->connect()) 
				internalServerError($sql->getError());
			if(!isset($_GET["timestamp"])) {
				echo json_encode($sql->getFreshUpdates());	
			} else {
				$timestamp = $_GET["timestamp"];
				//ensure parameter is all digits and proper length for timestamp
				if(!isTimestamp($timestamp))
					malformedRequest();
				$result = $sql->getUpdates($timestamp);
				echo json_encode($result);
			}
		}
}

function getID() {
	return end(explode('/',$_SERVER['REQUEST_URI']));
}

function isTimestamp($str) {
	return strlen($str)==14&&ctype_digit($str);
}
