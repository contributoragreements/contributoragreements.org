EFG - Extended Form Generator for Contao Open Source CMS Changelog
==================================================================


Version 2.2.1 stable (2014-02-13)
---------------------------------

### Changed
Support using closures as DCA callbacks

### Changed
Do not pass POST data to the 'deserialize()' function

### Fixed
Installing or updating EFG when running Contao in 'coreOnlyMode' did throw a fatal error 'Class Formdata not found'

### Changed
Changed .htaccess in ./assets for Apache 2.4 (see #49)

### Fixed
Generating alias in mode 'edit all' did not use the configured field


Version 2.2.0 stable (2013-12-31)
---------------------------------

### Changed
Adapted runonce.php to Contao 3.2, convert fileTree database fields,
changes related to Contao 3.2's UUID in the database assisted file system

### Changed
Changed templates folder structure

### Changed
Use Database\Statement::execute() instead of deprecated Database\Statement::executeUncached()

### Added
The target charset for converting from UTF-8 when exporting form data can now be given by configuration setting
$GLOBALS['EFG']['exportConvertToCharset'] = 'TARGET_CHARSET'; // default is 'CP1252'

NB: The conversion can be deactivated by configuration setting
$GLOBALS['EFG']['exportUTF8Decode'] = false;


Version 2.1.0 stable (2013-12-28)
---------------------------------

### Fixed
Backend filter menu could generate invalid SQL when filtering on form data details field

### Changed
Internal cache is rebuilt when updating form data config and dca files (on saving form in form generator)

### Fixed
Fixed path of icons folder used in frontend module 'listing form data'

### Fixed
Removed invalid relation definition causing a fatal error in Contao 3.2.x

### Fixed
Fixed issue with unwanted data stored in $_SESSION['FORM_DATA'] and $arrSubmitted (see #34)

### Fixed
Fixed wrong (double) backend filter panel

### Fixed
Order by numeric values in frontend module 'Listing form data' did not
work with decimal values (see #40)

### Fixed
Check if the folder system/cache/dca exists before scanning a non existing folder.
(Thanks to David Maack)

### Fixed
Ignore invisible form fields when checking for multipage form

### Changed
Adapted backend file selector to Contao 3.1

### Fixed
Handle field labels containing % (see #25)

### Fixed
Do not strip tags from listing condition (see #28)

### Fixed
Details view of frontend module 'listing form data' did not work
on page with alias 'index'

### Fixed
File uploads have not been validated on last page of a multi-page form

### Fixed
Form fields of type 'Select menu (DB)', 'Checkbox menu (DB)' and 'Radio button menu (DB)'
did not work if using form data as source table (see #26)


Version 2.0.1 stable (2013-03-17)
---------------------------------

### Fixed
Editing or deleting form data in module 'Listing form data' did not work
when using foreign tables in 'condition'

### Fixed
Backend filter menu did not keep search field after performing search

### Fixed
Frontend module 'Listing form data' did not show '0' values


Version 2.0.0 stable (2013-03-09)
------------------------------

### Added
Support extension 'cm_alternativeforms'

### Fixed
Using foreign tables in 'condition' of module 'Listing form data'
could result in invalid SQL statement


Version 2.0.0 rc2 (2013-02-23)
------------------------------

### Added
Support extension 'conditionalforms'

### Added
Add a CSS class to multipage forms

### Added
Added option to swap order of submit and back button of form field type
'Submit field and page break'

### Fixed
Date of formdata has been displayed as timestamp in backend formdata list view
'All results' / 'Feedback'

### Fixed
File attachments of confirmation email have been attached
to information (formatted text / html) email

### Fixed
Avoid warning message in runonce.php when trying to clear not existing cache

### Fixed
Added missing fallback to parent::__get() in Formdata::__get()

### Fixed
Replace inserttags in sender and sender name of confirmation mail

### Changed
Use method splitFriendlyName to parse email addresses

### Changed
Field 'Sorting value' (tl_formdata.sorting) can be edited in backend
