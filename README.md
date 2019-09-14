# fpbx-yealink-xmlcontacts

The purpose of this php Script is to export the contacts list from your FreePBX to your yealink phones using the Yealink Remote Address Book.

This script is a fork of this php script this script:

https://github.com/sorvani/freepbx-helper-scripts/blob/master/ContactManager_to_Yealink_AddressBook/cm_to_yl_ab.php

The original script created a name and for every number. As a result you end up with a long flat file with duplicate names for each unique number for that contact:

For example:

John Brown 011212122
John Brown 093724313
Jack Spade 230947037

This updated script here adds a second layer to the hierarchy, just like your cell phone works.  Its nothing special, yealink provides this capacity, we are just using it.

John Brown 011212122
  Office 011212122
  Cell 093724313
Jack Spade 230947037
  Office 230947037

The first number in your in your FPBX contact is used as a master contact number for erach contact name.  To see the rest of the numbers for that same contact name your need to click though Option>Detail on your phone to access them.

1. Copy the file into /var/www/html/
2. Change permissions to 655
3. Change owner to asterisk:asterisk 
4. Enable remote address book add the entry to your  
    https://serveraddress.com/filename

Note that you need php-pear installed for the script work. This is standard in v13 but not on v14.


