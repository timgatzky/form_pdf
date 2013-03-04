<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @copyright Tim Gatzky 2012
 * @author  Tim Gatzky <info@tim-gatzky.de>
 * @package  form_pdf
 * @link  http://contao.org
 * @license  http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Palettes
 */
foreach($GLOBALS['TL_DCA']['tl_form_field']['palettes'] as $type => $palette)
{
	if($type == '__selector__' || $type == 'default')
	{
		continue;
	}
	
	$palette .= ';{form_pdf_legend:hide},pdf_hide;';

	$GLOBALS['TL_DCA']['tl_form_field']['palettes'][$type] = $palette;
}

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form_field']['fields']['pdf_hide'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['pdf_hide'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('tl_class'=>'clr')
);