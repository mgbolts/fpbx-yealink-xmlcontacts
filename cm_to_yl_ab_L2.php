<?php
/*
The purpose of this file is to read all the Contact Manager entries for the specified group
and then output them in a Yealink Remote Address Book formatted XML syntax.

Instructions on how to use can be found here:
https://mangolassi.it/topic/18647/freepbx-contact-manager-to-yealink-address-book
*/

// Please note that this file has been substantially amended to increase the functionality:
// a) Group all numbers for a common display name
// b) Sort the order by alpa on the display name
// c) Add labels to each phone number
// e) Use E164 number convention
// f) Allow the number labels to be customized.

// Edit this varibale to match the name of hte group in Contact Manager
$contact_manager_group = "User Manager Group";

header("Content-Type: text/xml");

// get the MySQL/MariaDB login information from the amportal configuration file.
define("AMP_CONF", "/etc/amportal.conf");

$file = file(AMP_CONF);
if (is_array($file)) {
    foreach ($file as $line) {
        if (preg_match("/^\s*([a-zA-Z0-9_]+)=([a-zA-Z0-9 .&-@=_!<>\"\']+)\s*$/",$line,$matches)) {
            $amp_conf[ $matches[1] ] = $matches[2];
        }
    }
}

require_once('DB.php'); //PEAR must be installed
$db_user = $amp_conf["AMPDBUSER"];
$db_pass = $amp_conf["AMPDBPASS"];
$db_host = $amp_conf["AMPDBHOST"];
$db_name = $amp_conf["AMPDBNAME"];

$datasource = 'mysql://'.$db_user.':'.$db_pass.'@'.$db_host.'/'.$db_name;
$db = DB::connect($datasource); // attempt connection

$type="getAll";
// This pulls every number in contact maanger that is part of the group specified by $contact_manager_group
$results = $db->$type("SELECT cen.number, cge.displayname, cen.type, cen.E164 FROM contactmanager_group_entries AS cge LEFT JOIN contactmanager_en$

//dump the result into an array.
foreach($results as $result){
    // The if staements provide the ability to re-lable the phone number type as you wish.
    // It also allows for setting the number display order to be changed for multi-number contacts.

    $sortorder = 0;

    if ($result[2] == "cell") {
        $result[2] = "Mobile";  // this is the label that will display on the phone
        $sortorder = 3;  // change this number to change its order in the list
    }

    if ($result[2] == "internal") {
        $result[2] = "Extension";  // this is the label that will display on the phone
        $sortorder = 1;  // change this number to change its order in the list
    }

    if ($result[2] == "work") {
        $result[2] = "Work";  // this is the label that will display on the phone
        $sortorder = 2;  // change this number to change its order in the list
    }

    if ($result[2] == "other") {
        $result[2] = "Other";   // this is the label that will display on the phone
        $sortorder = 4;  // change this number to change its order in the list
    }

    if ($result[2] == "home") {
        $result[2] = "Home";   // this is the label that will display on the phone
        $sortorder = 5;  // change this number to change its order in the list
    }

// This sorts the extensions array by two fields, the display name and then the sort order field
// To change the sort order of the labels, change the sort order number in the if statements above..

    $dname = array();
    $order = array();
    for ($i = 0; $i < count($extensions); $i++) {
        $dname[] = $extensions[$i][1];
        $sorder[] = $extensions[$i][4];
        }
    // now apply sort
    array_multisort($dname, SORT_ASC,
                    $sorder, SORT_ASC, SORT_NUMERIC,
                    $extensions);

// output the XML header info
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
// Output the XML root. This tag must be in the format XXXIPPhoneDirectory
// You may change the word Company below, but no other part of the root tag.

$previousdispname = "";
$firstloopflag = 1;
echo "<CompanyIPPhoneDirectory  clearlight=\"true\">\n";
$index = 0;
if (isset($extensions)) {
    // Loop through the results and output them correctly.
    // Spacing is setup below in case you wish to look at the result in a browser.


    foreach ($extensions as $key=>$extension) {
        $index= $index + 1;

        // If its a new display name and not the first loop, close the number list for this display name directory collection
        if ($extension[1] != $previousdispname && $firstloopflag == 0) {
                echo "    </DirectoryEntry>\n";
                }

        // If its a new display name create directory entry and add a tag for the name
        if ($extension[1] != $previousdispname) {
                echo "    <DirectoryEntry>\n";
                echo "        <Name>$extension[1]</Name>\n";
                }

        echo "        <Telephone label=\"$extension[2]\">$extension[0]</Telephone>\n";
        //default is the phone exactly as input in fpbx, change $extensions[0] to [2] if you want the E164 format as created by fpbx.
        //To use E164, make sure you select the country for each number in each contact in FBX.
        //This feature is only available or external numbers, not internal contacts. 

        $firstloopflag = 0;
        $previousdispname = $extension[1];
    }
}
// Output the closing tag of the root. If you changed it above, make sure you change it here.
                
echo "    </DirectoryEntry>\n";
echo "</CompanyIPPhoneDirectory>\n";
?>

                      
