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

$GLOBALS['TL_DCA']['tl_form']['config']['onload_callback'][] = array('tl_form_form_pdf', 'modifyDca');

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
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['form_pdf'] = 'form_pdf_template,form_pdf_attachment';
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['form_pdf_confirmation'] = 'form_pdf_template_confirmation,form_pdf_attachment_confirmation';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_template'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_template'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_form_form_pdf', 'getPdfTemplates'),
	'eval'                    => array()
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_attachment'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_attachment'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array()
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_confirmation'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_template_confirmation'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_template'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_form_form_pdf', 'getPdfTemplates'),
	'eval'                    => array()
);

$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_attachment_confirmation'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_attachment'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array()
);


$GLOBALS['TL_DCA']['tl_form']['fields']['form_pdf_plugin'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_plugin'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'				  => array('tcpdf','dompdf'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_form']['form_pdf_plugin'],
	'eval'                    => array('tl_class'=>'w50 clr')
);



/**
 * class: tl_form_digitaldeliveryapp
 */
class tl_form_form_pdf extends Backend
{
	/**
	 * Modify the DCA on the fly
	 * @param object
	 */
	public function modifyDca(DataContainer $dc)
	{
		// check if efg is running
		if(in_array('efg', $this->Config->getActiveModules()))
		{
			// regular
			$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace
			(
			   'sendFormattedMail',
			   'sendFormattedMail,form_pdf',
			   $GLOBALS['TL_DCA']['tl_form']['palettes']['default']
			);
			
			// confirmation
			$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace
			(
				'sendConfirmationMail',
				'sendConfirmationMail,form_pdf_confirmation',
				$GLOBALS['TL_DCA']['tl_form']['palettes']['default']
			);
		}
		else
		{
			$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace
			(
				'sendViaEmail',
				'sendViaEmail,form_pdf',
				$GLOBALS['TL_DCA']['tl_form']['palettes']['default']
			);
		}
	}
	
	/**
	 * Return all pdf templates as array
	 * @param DataContainer
	 * @return array
	 */
	public function getPdfTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('pdf_', $dc->id);
	}	
}
