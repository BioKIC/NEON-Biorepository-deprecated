<?php
/*
------------------
Language: English
------------------
*/

include_once($SERVER_ROOT.'/content/lang/collections/editor/occurrenceeditor.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/content/lang/collections/individual/index.'.$LANG_TAG.'.php');


$LANG['OCC_SKEL_SUBMIT'] = 'Occurrence Skeletal Record Submission';
$LANG['HOME'] = 'Home';
$LANG['COL_MNGMT'] = 'Collection Management';
$LANG['DISPLAY_INSTRUCTIONS'] = 'Display Instructions';
$LANG['SKELETAL_DATA'] = 'Skeletal Data';
$LANG['DISPLAY_OPTIONS'] = 'Display Options';
$LANG['TOOL_DESCRIPTION'] = 'Description of Tool';
$LANG['SKELETAL_DESCIPRTION_1'] = 'This page is typically used to enter skeletal records into the system during the imaging process. Since collections are
						commonly organized by scientific name, country, and state, it takes little extra effort for imaging teams to
						collect this information while they are imaging specimens. The imaging team enters the basic collection
						information shared by the batch of specimens being processed, and then each time they scan a barcode into the catalog
						number field, a record is added to the system primed with the catalog number and skeletal data.';
$LANG['SKELETAL_DESCIPRTION_2'] = 'More complete data can be entered by clicking on the catalog number, but the recommended workflow is to process the full label
						data directly from the image of the specimen label at a later stage. An image can also be uploaded by clicking on the image
						symbol to the right of the catalog number, but images typically need to be adjusted before they are ready for upload (e.g. resized, light balanced).
						Furthermore, projects that store their images on remote image servers will
						typically require separate workflows for batch processing images. Contact your project / portal manager to find out
						the preferred way to load specimen images.';
$LANG['SKELETAL_DESCIPRTION_3'] = 'Click the Display Option symbol located above scientific name to adjust field display and preferred action when a record
						already exists for a catalog number. By default, a new record will not be created if the catalog number already exists.
						However, a secondary option is available that will append skeletal data into empty fields of existing records.
						Skeletal data will not copy over existing field values.';
$LANG['OPTIONS'] = 'Options';
$LANG['X_CLOSE'] = 'X Close';
$LANG['FIELD_DISPLAY'] = 'Field Display';
$LANG['OTHER_CAT_NUMS'] = 'Other Catalog Numbers';
$LANG['COUNTY_PARISH'] = 'County/Parish';
$LANG['COLLECTOR_NO'] = 'Collector Number';
$LANG['COLLECTION_DATE'] = 'Collection Date';
$LANG['CATNUM_MATCH'] = 'Catalog Number Match Action';
$LANG['RESTRICT_IF_EXISTS'] = 'Restrict entry if record exists';
$LANG['APPEND_VALUES'] = 'Append values to existing records';
$LANG['SESSION'] = 'Session';
$LANG['COUNT'] = 'Count';
$LANG['RATE'] = 'Rate';
$LANG['PER_HOUR'] = 'per hour';
$LANG['ADD_NAME_THESAURUS'] = 'Add new name to taxonomic thesaurus';
$LANG['PROTECT_LOCALITY'] = 'Protect locality details from general public';
$LANG['UNPROCESSED'] = 'unprocessed';
$LANG['STAGE_1'] = 'stage 1';
$LANG['STAGE_2'] = 'stage 2';
$LANG['STAGE_3'] = 'stage 3';
$LANG['PENDING_REVIEW_NFN'] = 'pending review-nfn';
$LANG['REVIEWED'] = 'reviewed';
$LANG['CLOSED'] = 'closed';
$LANG['RECORDS'] = 'Records';
$LANG['NOT_AUTHORIZED'] = 'You are not authorized to acces this page.';
$LANG['CONTACT_ADMIN'] = 'Contact an administrator to obtain the necessary permissions';
$LANG['ERROR_NO_ID'] = 'ERROR: collection identifier not set';

?>