<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		form_pdf
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

// make sure its the last hook processed
if(in_array('efg', $this->getActiveModules()) || in_array('efg', \Config::getInstance()->getActiveModules() ))
{
	array_insert($GLOBALS['TL_HOOKS']['processEfgFormData'],count($GLOBALS['TL_HOOKS']['processEfgFormData']),array(array('FormPDF','processEfgFormData')));
}
else
{
	array_insert($GLOBALS['TL_HOOKS']['processFormData'],count($GLOBALS['TL_HOOKS']['processFormData']),array(array('FormPDF','processFormData')));
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('FormPDFInsertTags', 'replaceTags');


/**
 * Globals
 */
$GLOBALS['FORM_PDF']['path'] = 'files';
$GLOBALS['FORM_PDF']['filename'] = 'MyPDF';
$GLOBALS['FORM_PDF']['path_confirmation'] = 'files';
$GLOBALS['FORM_PDF']['filename_confirmation'] = 'MyConfirmationPDF';
$GLOBALS['FORM_PDF']['uniqueFilename'] = false; // adds a timestamp to the filename when a file with the same name already exists
$GLOBALS['FORM_PDF']['dompdf_path'] = 'assets/dompdf';
$GLOBALS['FORM_PDF']['swiftmailer'] = 'vendor/swiftmailer/swiftmailer/lib/swift_required.php';
if (version_compare(VERSION, '3.0', '>=') && version_compare(VERSION, '3.2', '<='))
{
	$GLOBALS['FORM_PDF']['swiftmailer'] = 'system/core/vendor/swiftmailer/swift_required.php';
}

if (version_compare(VERSION, '2.11', '<=') )
{
	$GLOBALS['FORM_PDF']['dompdf_path'] = 'plugins/dompdf';
	$GLOBALS['FORM_PDF']['path'] = 'tl_files';
	$GLOBALS['FORM_PDF']['path_confirmation'] = 'tl_files';
	$GLOBALS['FORM_PDF']['swiftmailer'] = '/plugins/swiftmailer/swift_required.php';
}