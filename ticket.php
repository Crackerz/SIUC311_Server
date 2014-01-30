<?php
class Ticket {
	public $id;
	public $reportType;
	public $dawgTag;
	public $description;
	public $latitude;
	public $longitude;
	public $timestamp;
	public $status;

	public static function ticketFromJson(&$obj) {
		$result = new Ticket();
		//Grab all property names of this object and populate them if
		//the json object had the same names declared
		$properties = array_keys(get_object_vars($result));
		foreach($properties as $property) {
			if(property_exists($obj,$property)) {
				$result->$property = $obj->$property;
			}
		}
		return $result;
	}

	public static function ticketFromSQL($row) {
		$result = new Ticket();
		$result->id = $row[0];
		$result->dawgTag = $row[1];
		$result->reportType = $row[2];
		$result->description = $row[3];
		$result->latitude = $row[4];
		$result->longitude = $row[5];
		$result->timestampe = $row[6];
		$result->status = $row[7];
		return $result;
	}
}

//This structure wraps all elements in an update response
class Update {
		public $tickets;

		public function __construct() {
			$this->tickets = array();
		}

		public function addTicket($ticket) {
			array_push($this->tickets,$ticket);
		}
}
