<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		form_pdf
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Class file
 * TableFormFormPDF
 */
class TableFormFormPDF extends \Backend
{
	/**
	 * Modify the DCA
	 * @param object
	 */
	public function modifyDca(\DataContainer $objDC)
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
		
		return $objDC;
	}
	
	/**
	 * Return all pdf templates as array
	 * @param DataContainer
	 * @return array
	 */
	public function getPdfTemplates(\DataContainer $objDC)
	{
		$intPid = $objDC->activeRecord->pid;

		if (\Input::getInstance()->get('act') == 'overrideAll')
		{
			$intPid = \Input::getInstance()->get('id');
		}

		return $this->getTemplateGroup('pdf_', $intPid);
	}	
}