<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		form_digitaldelivery
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Hooks
 */

// make sure its the last hook processed
if(in_array('efg', $this->getActiveModules()))
{
	$max = count($GLOBALS['TL_HOOKS']['processEfgFormData']);
	array_insert($GLOBALS['TL_HOOKS']['processEfgFormData'],$max,array(array('FormPDF','processEfgFormData')));
}
else
{
}

// Replace Insert tags
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('FormPDF', 'replaceTags');


/**
 * Globals
 */
$GLOBALS['FORM_PDF']['path'] = 'tl_files';
$GLOBALS['FORM_PDF']['filename'] = 'MyPDF';
$GLOBALS['FORM_PDF']['path_confirmation'] = 'tl_files';
$GLOBALS['FORM_PDF']['filename_confirmation'] = 'MyConfirmationPDF';
$GLOBALS['FORM_PDF']['uniqueFilename'] = false; // adds a timestamp to the filename when a file with the same name already exists
$GLOBALS['FORM_PDF']['dompdf_path'] = 'assets/dompdf';
if (version_compare(VERSION, '2.11', '<=') )
{
	$GLOBALS['FORM_PDF']['dompdf_path'] = 'plugins/dompdf';
}