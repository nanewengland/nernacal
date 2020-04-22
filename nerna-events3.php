<?php
// PHP Gets mad if rely on the servers timezone (understably so) so we must set one.
date_default_timezone_set("America/New_York");

// Load dependencies
// Sabre decodes ical documents
use Sabre\VObject;
include 'vendor/autoload.php';

// Get iCal file from all in one events
$calendar = VObject\Reader::read(file_get_contents("https://nerna.org/?plugin=all-in-one-event-calendar&controller=ai1ec_exporter_controller&action=export_events&no_html=true"));
//$yolo = json_encode($calendar->jsonSerialize());
// date format
$date_now = date("Y-m-d");

$events = [];

foreach($calendar->VEVENT as $event) {
	if ($event->DTSTART->getDateTime()->format("Y-m-d") > $date_now) {
		if ($event->CATEGORIES == 'Regional Events') {
		    # echo $event->SUMMARY . "\n";
			$events[]["title"] = $event->SUMMARY;
		}
	}
}

//header("Access-Control-Allow-Origin: *");
echo json_encode($events);
