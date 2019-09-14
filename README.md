# fpbx-yealink-xmlcontacts
php Script to Export Contacts to Yealink Remote Address Book

This is edited php script which builds on this script:

https://github.com/sorvani/freepbx-helper-scripts/blob/master/ContactManager_to_Yealink_AddressBook/cm_to_yl_ab.php

The original script created a name and for every number for every unique number.  As a rsult you end up with a long flat file wil duplicate names with different numbers:

For example:

John Brown 011212122
John Brown 093724313
Jack Spade 230947037

The updated script here adds a second layer to the hierarchy, just like your cell phone works:

John Brown 011212122
  Office 011212122
  Cell 093724313
Jack Spade 230947037
  Office 230947037

The first number in your in your FPBX contact is used as a master contact.  To see the rest of the numbers for that contact name your need to click though Option>Detail to access.

Note that you need php-pear installed for the script work. This is standard in v13 but not on v14.

1. Copy the file into /var/www/html/
2. Change permissions to 655
3. Change owner to asterisk:asterisk 
4. Enable remote address book add the entry to your  
    https://serveraddress.com/filename


