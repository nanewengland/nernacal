<?php
/**
\file nernacal.php

\brief Gets list of upcoming events from Timely Events Plugin and outputs as PDF.

Problem:
- Members and trusted servants requested a way to quickly print a plain pdf list of events on nerna.org to bring to their area.

Solution:
- This file, gets iCal list of events and converts to object. then we format and output to PDF.

Where is this in use on NERNA.ORG?
- Currently the only place this is on is the calendar page https://nerna.org/calendar/

This file is part of the New England Region of Narcotics Anonymous Website.
URL: https://www.nerna.org

Author: if help is needed or have questions you can reach out to an addict named Patrick J (Martha's Vineyar).
Patrick Joyce
pjaudiomv@gmail.com
508-939-1663

 */

// PHP Gets mad if rely on the servers timezone (understably so) so we must set one.
date_default_timezone_set("America/New_York");

// Load dependencies
// Sabre decodes ical documents
use Sabre\VObject;
include 'vendor/autoload.php';

// Get iCal file from all in one events
$vcalendar = VObject\Reader::read(file_get_contents("https://nerna.org/?plugin=all-in-one-event-calendar&controller=ai1ec_exporter_controller&action=export_events&no_html=true"));
// date format
$date_now = date("Y-m-d");

// Traverse iCal objects
$events = [];
foreach($vcalendar->VEVENT as $event) {
    if ($event->DTSTART->getDateTime()->format("Y-m-d") > $date_now) {
        if ($event->CATEGORIES != 'Area Service Committees') {
            $start_timestamp = $event->DTSTART->getDateTime()->format("U");
            if (isset($event->DTEND)) {
                $date = strval($event->DTSTART->getDateTime()->format("F d, Y  g:i A")) . " - ". strval($event->DTEND->getDateTime()->format("F d, Y  g:i A"));
            } else {
                $date = strval($event->DTSTART->getDateTime()->format("F d, Y  g:i A"));
            }
            if (strlen($event->CONTACT) >= 7) {
                $contact = strval($event->CONTACT);
            } else {
                $contact = '';
            }
            if (isset($event->{'X-COST'})) {
                $cost = strval($event->{'X-COST'});
            } else {
                $cost = '';
            }
            if (isset($event->CATEGORIES)) {
                $category = strval($event->CATEGORIES);
            } else {
                $category = '';
            }
            if (isset($event->DESCRIPTION)) {
                $description = strval($event->DESCRIPTION);
            } else {
                $description = '';
            }
            $events[] = array(
                'summary' => strval($event->SUMMARY),
                'location' => strval($event->LOCATION),
                'date'    => $date,
                'contact'  => $contact,
                'cost' => $cost,
                'category' => $category,
                'description' => $description,
                'url' => strval($event->URL),
                'start_ts' => strval($start_timestamp)
            );
        }
    }
}

usort($events, function ($a, $b) {
    return strnatcasecmp($a['start_ts'], $b['start_ts']);
});

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
echo json_encode($events);
