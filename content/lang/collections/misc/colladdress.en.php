<?php
/*
------------------
Language: English
------------------
*/

include_once($SERVER_ROOT.'/content/lang/collections/misc/sharedterms.'.$LANG_TAG.'.php');

$LANG['MAILING_ADD'] = 'Mailing Address';
$LANG['NEED_INST_CODE'] = 'Institution Code must have a value';
$LANG['NEED_COLL_VALUE'] = 'Collection Name must have a value';
$LANG['CANNOT_GUID'] = 'The Symbiota Generated GUID option cannot be selected for a collection that is managed locally outside of the data portal (e.g. Snapshot management type). In this case, the GUID must be generated within the source collection database and delivered to the data portal as part of the upload process.';
$LANG['NEED_DECIMAL'] = 'Latitude and longitude values must be in the decimal format (numeric only)';
$LANG['NEED_RIGHTS'] = 'Rights field (e.g. Creative Commons license) must have a selection';
$LANG['SORT_NUMERIC'] = 'Sort sequence must be numeric only';
$LANG['AGG_GUID'] = 'An Aggregate dataset (e.g. specimens coming from multiple collections) can only have occurrenceID selected for the GUID source';
$LANG['NEED_GUID'] = 'You must select a GUID source in order to publish to data aggregators.';
$LANG['SEL_INST'] = 'Select an institution to be linked';
$LANG['NOT_SUPP'] = 'The file you have uploaded is not a supported image file. Please upload a jpg, png, or gif file.';
$LANG['NOT_SUPP_URL'] = 'The url you have entered is not for a supported image file. Please enter a url for a jpg, png, or gif file.';
$LANG['MAILING_ADDS'] = 'Mailing Addresses';
$LANG['EDIT_ADD'] = 'Edit Institution Address';
$LANG['UNLINK_ADD'] = 'Unlink institution address';
$LANG['NO_ADDS'] = 'No addesses linked';
$LANG['SEL_ADD'] = 'Select Institution Address';
$LANG['ADD_INST'] = 'Add an institution not on list';

?>