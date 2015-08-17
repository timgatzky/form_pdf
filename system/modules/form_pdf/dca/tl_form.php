<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @copyright	Tim Gatzky 2012
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		form_pdf
 * @link  		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

$GLOBALS['TL_DCA']['tl_form']['config']['onload_callback'][] = array('TableFormFormPDF', 'modifyDca');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'form_pdf';
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'form_pdf_confirmation';


// pdf plugin field
$GLOBALS['TL_DCA']['tl_form']['palettes'] = str_replace
(
	'formID', 
	'formID,form_pdf_plugin', 
	$GLOBALS['TL_DCA']['tl_form']['palettes']
);

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['form_pdf'] = 'form_pdf_template,form_pdf_attachment,form_pdf_paper';
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['form_pdf_confirmation'] = 'form_pdf_template_confirmation,form_pdf_attachment_confirmation,form_pdf_paper';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_template'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_template'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('TableFormFormPDF', 'getPdfTemplates'),
	'eval'                    => array(),
	'sql'					  => "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_attachment'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_attachment'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array(),
	'sql'					  => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_confirmation'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_template_confirmation'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_template'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('TableFormFormPDF', 'getPdfTemplates'),
	'eval'                    => array(),
	'sql'					  => "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_attachment_confirmation'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_attachment'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array(),
	'sql'					  => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_plugin'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_plugin'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'				  => array('tcpdf','dompdf'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_plugin'],
	'eval'                    => array('tl_class'=>'w50 clr'),
	'sql'					  => "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_paper'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_paper'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'default'				  => 'A4',
	'options'				  => $GLOBALS['FORM_PDF']['papers'] ?: array('A3'),
	'eval'                    => array('tl_class'=>'w50 clr','chosen'=>true),
	'sql'					  => "varchar(32) NOT NULL default ''",
);


