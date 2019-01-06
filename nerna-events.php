<?php
/**
    \file nerna-events.php
    
    \brief Gets list of upcoming events from Timely Events Plugin and outputs as basic html for the NA New England App.
    
	Problem:
    	- Members and trusted servants requested a way to quickly print a plain pdf list of events on nerna.org to bring to their area.
	
	Solution:
		- This file, gets iCal list of events and converts to object. then we format and output as html for the NA New England App.
	
	Where is this in use on NERNA.ORG?
		- Currently the only place this is on is the calendar page https://nerna.org/calendar/
	
    This file is part of the New England Region of Narcotics Anonymous Website.
	URL: https://www.nerna.org
	
	Author: if help is needed or have questions you can reach out to an addict named Patrick J (Martha's Vineyard).
		Patrick Joyce
		pjaudiomv@gmail.com
		508-939-1663
		
 */

// PHP Gets mad if rely on the servers timezone (understably so) so we must set one.
date_default_timezone_set("America/New_York");

// Load dependencies
// Sabre decodes ical documents
use Sabre\VObject;
// Mpdf converts html/css to PDF.
include 'vendor/autoload.php';

// Get iCal file from all in one events
$vcalendar = VObject\Reader::read(file_get_contents("https://nerna.org/?plugin=all-in-one-event-calendar&controller=ai1ec_exporter_controller&action=export_events&no_html=true"));
// date format
$date_now = date("Y-m-d");

// Traverse iCal objects
$epdf .= '<div class="nerna-event">';
foreach($vcalendar->VEVENT as $event) {
  if ($event->DTSTART->getDateTime()->format("Y-m-d") > $date_now) {
    if ($event->CATEGORIES != 'Area Service Committees') {

        

        $epdf .= '<div class="card-title"><strong>'. $event->SUMMARY. '</strong></div>';

        $epdf .= "<p><strong>Date:</strong> ". $event->DTSTART->getDateTime()->format("F d, Y  g:i A");
        if (isset($event->DTEND)) {
            $epdf .= " - ". $event->DTEND->getDateTime()->format("F d, Y  g:i A"). "<br>";
        }
        $epdf .= "<strong>Location:</strong> ". $event->LOCATION. "<br>";
        if (strlen($event->CONTACT) >= 7) {
            $epdf .= "<strong>Contact: </strong>" .$event->CONTACT. "<br>";
        }
        if (isset($event->{'X-COST'})) {
            $epdf .= "<strong>Cost:</strong> ".$event->{'X-COST'}. "<br>";
        }
        if (isset($event->CATEGORIES)) {
            $epdf .= "<strong>Category:</strong> ".$event->CATEGORIES. "<br>";
        }
        if (isset($event->DESCRIPTION)) {
            $epdf .= $event->DESCRIPTION. '<br><hr style="color:blue;">';
        }
		$epdf .= '</p>';
        
    }
  }
}
$epdf .= '</div>';
echo $epdf;

?>
