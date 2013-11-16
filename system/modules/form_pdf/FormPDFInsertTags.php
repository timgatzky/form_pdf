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

class FormPDFInsertTags extends \Controller
{
	/**
	 * Replace Insert Tags
	 * @param string
	 * @return mixed
	 * called from parseInsertTags HOOK
	 */
	public function replaceTags($strTags)
	{
		global $objPage;
		$elements = explode('::', $strTags);

		switch (strtolower($elements[0]))
		{
			case 'form_pdf':
				switch($elements[1])
				{
					case 'file':
						$this->import('Session');
						$arrSession = $this->Session->get('form_pdf');
						return $arrSession['file'];		
					break;
					case 'link':
						$this->import('Session');
						$arrSession = $this->Session->get('form_pdf');
						
						$file = $arrSession['file'];
						$filename = basename($file);
						$href = $this->replaceInsertTags('{{env::url}}') . '/' . $file;
						
						return '<a href="'.$href.'" title="'.$filename.'">'.$filename.'</a>';
					break;
					case 'link':
						$this->import('Session');
						$arrSession = $this->Session->get('form_pdf');
						
						$file = $arrSession['file'];
						return $this->replaceInsertTags('{{env::url}}') . '/' . $file;
					break;
					case 'file_confirmation':
						$this->import('Session');
						$arrSession = $this->Session->get('form_pdf');
						return $arrSession['file_confirmation'];		
					break;
					case 'link_confirmation':
						$this->import('Session');
						$arrSession = $this->Session->get('form_pdf');
						
						$file = $arrSession['file_confirmation'];
						$filename = basename($file);
						$href = $this->replaceInsertTags('{{env::url}}') . '/' . $file;
						
						return '<a href="'.$href.'" title="'.$filename.'">'.$filename.'</a>';
					break;
					case 'link_url_confirmation':
						$this->import('Session');
						$arrSession = $this->Session->get('form_pdf');
						
						$file = $arrSession['file_confirmation'];
						$href = $this->replaceInsertTags('{{env::url}}') . '/' . $file;
						
						return $href;
					break;
					default:
						return false;
					break;
				}
			break;
			case 'form':
		        if(isset($_SESSION['FORM_DATA'][$elements[1]])) 
		        {
		        	return $_SESSION['FORM_DATA'][$elements[1]];
		        }
		        return false;
	        break; 
	        
			default: 
				return false;
			break;
		}
	}	
}