<?php
include_once './ticket.php';
include_once './httpResponse.php';

//DEFINES THE INTERACTION BETWEEN OUR SERVER AND SQL
class SQLServer {
	private $SERVER_ADDRESS = "127.0.0.1";
	private $SERVER_USER = "root";
	private $SERVER_PASSWORD = "";
	private $SERVER_DATABASE = "311";
	private $TICKET_TABLE = "tickets";

	public $connection;

	public function connect() {
		$this->connection = mysqli_connect($this->SERVER_ADDRESS,$this->SERVER_USER,$this->SERVER_PASSWORD,$this->SERVER_DATABASE);
		if(mysqli_connect_errno($this->connection)) {
			$connection = null;
			return false;
		}
		return true;
	}

	public function getError() {
		return $this->connection->error;
	}

	public function insertTicket(&$ticket) {
		$query = "INSERT INTO `".$this->TICKET_TABLE."` (`reportType`,`dawgTag`,`description`,`latitude`,`longitude`) VALUES ('".$ticket->reportType."','".$ticket->dawgTag."','".$ticket->description."',".$ticket->latitude.",".$ticket->longitude.")";
		return $this->connection->query($query);
	}

	public function getTicket($id) {
		$query = "SELECT * FROM `".$this->TICKET_TABLE."` WHERE `id` = ".$id." AND `status` != 2";
		if(!($result = $this->connection->query($query))) {
			internalServerError($this->getError()); //httpResponse.php
		} 
		if(!($row = $result->fetch_row())) {
			resourceNotFound();
		}
		return Ticket::ticketFromSQL($row);
	}

	public function deleteTicket($id) {
		$query =  "UPDATE `".$this->TICKET_TABLE."` SET `status` = 2 WHERE `id` = ".$id;
		return $this->connection->query($query);
	}

	public function updateTicket($id,&$ticket) {
		$array = get_object_vars($ticket);
		$properties = array_keys($array);
		$query = "UPDATE `".$this->TICKET_TABLE."` SET";
		foreach($properties as $property) {
			if(isset($array[$property])&&$property!="timestamp"&&$property!="id") {
				$query = $query." `".$property."` = ";
				if(gettype($array[$property])=="string")
					$query = $query."'".$array[$property]."',";
				else
					$query = $query.$array[$property].',';
			}
		}	
		$query = substr($query, 0, -1); //remove last ','
		$query = $query." WHERE `id` = ".$id;
		if(!$this->connection->query($query))
			internalServerError($this->getError());
		return true;
	}

	public function getUpdates($timestamp) {
		$query = "SELECT * FROM `".$this->TICKET_TABLE."` WHERE `time` > '".$timestamp."'";
		if(!$result = $this->connection->query($query))
			internalServerError($this->getError());
		$update = new Update();
		while($row = $result->fetch_row()) {
			$update->addTicket(Ticket::ticketFromSQL($row));
		}
		return $update;
	}

	//Called when a client currently has no tickets stored
	public function getFreshUpdates() {
		$query = "SELECT * FROM `".$this->TICKET_TABLE."` WHERE `status` = 0";
		if(!$result = $this->connection->query($query))
			internalServerError($this->getError());
		$update = new Update();
		while($row = $result->fetch_row()) {
			$update->addTicket(Ticket::ticketFromSQL($row));
		}
		return $update;

	}
}
