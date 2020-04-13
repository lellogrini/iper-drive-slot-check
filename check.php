#!/usr/bin/env php
<?php
include_once "libs/console.php";

//You can find these variables inside docs/stores.json
$store = [
        "id" => 10155,//your store storeId
        "pdr"=> 4 //your store pdrId
    ];

//retrieve selected IPER Drive page
$storeURL = "https://iperdrive.iper.it/spesa-online/SlotDisplayView?sync=1&langId=-4&storeId=".$store["id"]."&pdrId=".$store["pdr"];
$iper = file_get_contents($storeURL);

//I've found that the slots information are stored in page as JSON contained
// in StoreLocatorJS.initOrari() JS function
if (preg_match('/StoreLocatorJS.initOrari(.*?);/', $iper, $display) === 1) {
    //Turn string into object trimming round brackets
    $data = json_decode(trim($display[1],"(\)"));
}else{
    Console::log('String not found', "red", true);
    die();
}

if (!isset($data->orario)) {
    Console::log('Bad JSON', "red", true);
} else {
    //find availabilities
    $availabilities = [];
    foreach ($data->orario as $day) {
        //should be 3 days
        $availabilities[$day->dayDate] = [];
        foreach ($day->slots as $slot) {
            //check if active is different from -1 and 0 (idk why they use -1)
            if ($slot->active != -1 && $slot->active != 0) {
                array_push($availabilities[$day->dayDate], $slot->title);
            }
        }
    }

    //check availabilities
    foreach ($availabilities as $date => $availability) {
        if (count($availability) > 0) {
            //There is at least one availability so should notify
            Console::log("There are some available slots for $date", "green", true);
        } else {
            Console::log("There are no available slots for $date", "red", true);
        }
    }
}

?>