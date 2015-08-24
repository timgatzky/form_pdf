<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @copyright 	Tim Gatzky 2012
 * @author  	Tim Gatzky <info@tim-gatzky.de>
 * @package  	form_pdf
 * @link  		http://contao.org
 * @license  	http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

class FormPDF extends \Backend
{
	/**
	 * Default Template
	 * @var string
	 */
	protected $strTemplate = 'form_pdf_example';

	/**
	 * Default plugin
	 * @var string
	 */
	protected $strPlugin = 'dompdf';
	
	/**
	 * @var boolean
	 */
	protected $bolIsConfirmation = false;
	
	/**
	 * Paper size
	 * @var string
	 */
	protected $strPaper = 'a4';
	
	
	/**
	 * Start the pdf creation without the efg extension
	 * @param array
	 * @param array
	 * @param array
	 */
	public function processFormData($arrPost, $arrForm, $arrFiles)
	{
		if(!$arrForm['form_pdf'] && !$arrForm['form_pdf_confirmation'])
		{
			return $arrPost;
		}
		
		// mimic efg fields
		$arrForm['formattedMailRecipient'] = $arrForm['recipient'];
		$arrForm['formattedMailSubject'] = $arrForm['subject'];
		
		// set paper size
		if(strlen($arrForm['form_pdf_pager']) > 0)
		{
			$this->strPaper = $arrForm['form_pdf_pager'];
		}
		
		$this->processEfgFormData($arrPost, $arrFiles, 0, $arrForm);
	}
	
	/**
	 * Generate a pdf file from a template html file and attach to email
	 * @param array
	 * @param array
	 * @param array
	 * @return array
	 * called from processEfgFormData HOOK (only called on email submit)
	 */
	public function processEfgFormData($arrSubmitted, $arrFiles, $intOldId, &$arrForm)
	{
		if(!$arrForm['form_pdf'] && !$arrForm['form_pdf_confirmation'])
		{
			return $arrSubmitted;
		}
		
		// set pdf plugin
		$this->strPlugin = $arrForm['form_pdf_plugin'];
		
		if(!$this->strPlugin)
		{
			return $arrSubmitted;
		}
		
		//-- generate pdf and attach
		$filename = '';
		$path = '';
		if($arrForm['form_pdf'])
		{
			$this->bolIsConfirmation = false;
			
			// set paper size
			if(strlen($arrForm['form_pdf_pager']) > 0)
			{
				$this->strPaper = $arrForm['form_pdf_pager'];
			}
			
			//-- get path to template file and write template
			$pdf_template = $arrForm['form_pdf_template'];
			if($this->strTemplate != $pdf_template)
			{
				$this->strTemplate = $pdf_template;
			}
	
			$arrFields = $arrSubmitted;
			unset($arrFields['FORM_SUBMIT']);
			unset($arrFields['MAX_FILE_SIZE']);
			unset($arrFields['REQUEST_TOKEN']);
	
			// unset fields with pdf_hide = 1
			$objDatabase = \Database::getInstance();
			foreach($arrFields as $field => $v)
			{
				$objFormField = $objDatabase->prepare("SELECT pdf_hide FROM tl_form_field WHERE pid=? AND name=?")
											->limit(1)
											->execute($arrForm['id'], $field);
				
				if($objFormField->numRows > 0 && $objFormField->pdf_hide > 0)
				{
					unset($arrFields[$field]);
				}
			}
			
			// HOOK: allow other extensions to modify the pdf output fields
			if (isset($GLOBALS['TL_HOOKS']['FORM_PDF']['getFormFields']) && count($GLOBALS['TL_HOOKS']['FORM_PDF']['getFormFields']) > 0)
			{
				foreach($GLOBALS['TL_HOOKS']['FORM_PDF']['getFormFields'] as $callback)
				{
					$this->import($callback[0]);
					$arrFields = $this->$callback[0]->$callback[1]($arrFields, $arrForm, $this);
				}
			}
			
			// output template
			$objTemplate = new \FrontendTemplate($this->strTemplate);
			$objTemplate->setData($this->arrData);
			$objTemplate->submitted = $arrSubmitted;
			$objTemplate->form = $arrForm;
			$objTemplate->fields = $arrFields;
			$objTemplate->files = $arrFiles;
	
			// generate template
			$strHtml = $objTemplate->parse();
			//--
				
			$filename = ($GLOBALS['FORM_PDF']['filename'] ? $GLOBALS['FORM_PDF']['filename'] : 'myPdf');
			$filename = $this->replaceInsertTags($filename);
            $path = ($GLOBALS['FORM_PDF']['path'] ? $GLOBALS['FORM_PDF']['path'].'/' : 'tl_files/');

			// save file or send directely to the browser
			if($arrForm['form_pdf_attachment'])
			{
				$strPdf = $this->printPDFtoFile($strHtml,$path,$filename,$GLOBALS['FORM_PDF']['uniqueFilename']);
			}
			else
			{
                $strPdf = $this->printPDFtoBrowser($strHtml,$filename);
			}
			
			//-- store current path in Session for further use
			$objSession = \Session::getInstance();
			$arrSession = $objSession->get('form_pdf');
			$arrSession = array('file'=>$strPdf);
			$objSession->set('form_pdf',$arrSession);
			//--
			
			//-- attachments
			if($arrForm['form_pdf_attachment'])
			{
				$arrConfirmationAttachments = deserialize($arrForm['formattedMailAttachments']);
	
				if(!is_array($arrConfirmationAttachments))
				{
					$arrConfirmationAttachments = array();
				}
				// check if the file is already attached
				if(!in_array($strPdf, $arrConfirmationAttachments))
				{
					$arrConfirmationAttachments[] = $strPdf;
				}
	
				if(count($arrConfirmationAttachments) > 0)
				{
					$arrForm['addFormattedMailAttachments'] = 1;
					$arrForm['formattedMailAttachments'] = serialize($arrConfirmationAttachments);
				}
			}
	
		}
		//--
		
		//-- generate confirmation pdf and attach
		$filename = '';
		$path = '';
		if($arrForm['form_pdf_confirmation'])
		{
			$this->bolIsConfirmation = true;
			
			// set paper size
			if(strlen($arrForm['form_pdf_pager_confirmation']) > 0)
			{
				$this->strPaper = $arrForm['form_pdf_pager_confirmation'];
			}
			
			//-- get path to template file and write template
			$pdf_template = $arrForm['form_pdf_template_confirmation'];
			if($this->strTemplate != $pdf_template)
			{
				$this->strTemplate = $pdf_template;
			}
	
			$arrFields = $arrSubmitted;
			unset($arrFields['FORM_SUBMIT']);
			unset($arrFields['MAX_FILE_SIZE']);
	
			// unset fields with pdf_hide = 1
			foreach($arrFields as $field => $v)
			{
				$objFormField = $this->Database->prepare("SELECT pdf_hide FROM tl_form_field WHERE pid=? AND name=?")
				->limit(1)
				->execute($arrForm['id'], $field);
				
				if($objFormField->numRows > 0 && $objFormField->pdf_hide > 0)
				{
					unset($arrFields[$field]);
				}
			}
			
			// HOOK: allow other extensions to modify the pdf output fields
			if (isset($GLOBALS['TL_HOOKS']['FORM_PDF']['getFormFields']) && count($GLOBALS['TL_HOOKS']['FORM_PDF']['getFormFields']) > 0)
			{
				foreach($GLOBALS['TL_HOOKS']['FORM_PDF']['getFormFields'] as $callback)
				{
					$this->import($callback[0]);
					$arrFields = $this->$callback[0]->$callback[1]($arrFields, $arrForm, $this);
				}
			}
			
			// output template
			$objTemplate = new \FrontendTemplate($this->strTemplate);
			$objTemplate->setData($this->arrData);
			$objTemplate->submitted = $arrSubmitted;
			$objTemplate->form = $arrForm;
			$objTemplate->fields = $arrFields;
			$objTemplate->files = $arrFiles;
	
			// generate template
			$strHtml = $objTemplate->parse();
			//--
				
			$filename = ($GLOBALS['FORM_PDF']['filename_confirmation'] ? $GLOBALS['FORM_PDF']['filename_confirmation'] : 'myPdf');
			$filename = $this->replaceInsertTags($filename);

            $path = ($GLOBALS['FORM_PDF']['path_confirmation'] ? $GLOBALS['FORM_PDF']['path_confirmation'].'/' : 'tl_files/');
			
			// save file or send directely to the browser
			if($arrForm['form_pdf_attachment_confirmation'])
			{
				$strPdf = $this->printPDFtoFile($strHtml,$path,$filename,false);
			}
			else
			{
				$strPdf = $this->printPDFtoBrowser($strHtml,$filename);
				
			}
		
			//-- store current path in Session for further use
			$objSession = \Session::getInstance();
			$arrSession = $objSession->get('form_pdf');
			$arrSession = array('file_confirmation'=>$strPdf);
			$objSession->set('form_pdf',$arrSession);
			//--
			
			//-- confirmation attachments
			if($arrForm['form_pdf_attachment_confirmation'])
			{
				$arrConfirmationAttachments = deserialize($arrForm['confirmationMailAttachments']);
	
				if(!is_array($arrConfirmationAttachments))
				{
					$arrConfirmationAttachments = array();
				}
				// check if the file is already attached
				if(!in_array($strPdf, $arrConfirmationAttachments))
				{
					$arrConfirmationAttachments[] = $strPdf;
				}
	
				if(count($arrConfirmationAttachments) > 0)
				{
					$arrForm['addConfirmationMailAttachments'] = 1;
					$arrForm['confirmationMailAttachments'] = serialize($arrConfirmationAttachments);
				}
			}
		}
		//--
		
		// Manually send the Email and redirect when using DOMPDF
		// DOMPDF kills all contao routines executed afterwards.
		if($this->strPlugin == 'dompdf' || $this->strPlugin == 'tcpdf')
		{
			global $objPage;
			$objJumpTo = $objPage;
			
			// set jump to page
			if($arrForm['jumpTo'] > 0)
			{
				$objDatabase = \Database::getInstance();
				$objJumpTo = $objDatabase->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($arrForm['jumpTo']);
			}
			
			// redirect url
			$redirect = $this->generateFrontendUrl($objJumpTo->row());
			
			// send mail
			if($arrForm['sendFormattedMail'])
			{
			   $sendMail = $this->sendMail($arrSubmitted,$arrForm,false);
			
			   if(!$sendMail)
			   {
			   		// throw error here
			   }
			}
			
			// send confirmation mail with attachment
			if($arrForm['sendConfirmationMail'] && $arrForm['form_pdf_attachment_confirmation'])
			{
				$sendMail = $this->sendMail($arrSubmitted,$arrForm,true);
			
				if(!$sendMail)
				{
					// throw error here
				}
			}
			
			#$this->redirect($redirect);
		}
		
		return $arrSubmitted;
	}
	
	/**
	 * Kind of sad but DOMPDF kills all scrips executed after the pdf routing
	 * Send the confirmation email
	 * @param array
	 * @return boolean
	 */
	protected function sendMail($arrSubmitted,$arrForm=null,$bolIsConfirmationMail=false)
	{
		$arrRecipients = array();
		$arrSenders = array();
		$arrAttachments = array();
		$strText = '';
		$strSubject = '';
		
		if(!$bolIsConfirmationMail)
		{
			// recipients
			if(strlen($arrForm['formattedMailRecipient']) > 0)
			{
				$arrRecipients = explode(',',$arrForm['formattedMailRecipient']);
			}
			
			// senders
			#$arrSenders = explode(',', $arrForm['confirmationMailSender']);
			if(count($arrSenders) < 1)
			{
				$arrSenders[] = $GLOBALS['TL_CONFIG']['adminEmail'];
			}
			
			// attachments
			$arrAttachments = deserialize($arrForm['formattedMailAttachments']);
			
			// body and subject
			$strText = $arrForm['formattedMailText'];
			
			$strSubject = $arrForm['formattedMailSubject'];
		}
		// is confirmation mail
		else
		{
			// recipients
			if(strlen($arrForm['confirmationMailRecipient']) > 0)
			{
				$arrRecipients = explode(',',$arrForm['confirmationMailRecipient']);
			}
			// get recipient email adress from POST
			if(strlen($arrForm['confirmationMailRecipientField']) > 0)
			{
				$f = $arrForm['confirmationMailRecipientField'];
				$arrRecipients[] = $arrSubmitted[$f];
			}
			
			// senders
			$arrSenders = explode(',', $arrForm['confirmationMailSender']);
			
			// attachments
			$arrAttachments = deserialize($arrForm['confirmationMailAttachments']);
			
			// body and subject
			$strText = $arrForm['confirmationMailText'];
			$strSubject = $arrForm['confirmationMailSubject'];
		}
		
		// check if a html template is set
		$isHtml = false;
		
		$arrHtmlTemplate = null;
		if(!$bolIsConfirmationMail)
		{
			$arrHtmlTemplate = $arrForm['formattedMailTemplate'];
		}
		else
		{
			$arrHtmlTemplate = $arrForm['confirmationMailTemplate'];
		}
		
		if($arrHtmlTemplate)
		{
			if (version_compare(VERSION, '3.0', '>='))
	        {
	        	$objFilesModel = new \FilesModel();
	        	$file = $objFilesModel->findMultipleByIds(array($arrHtmlTemplate));
	        	if($file->extension == 'html' || $file->extension == 'htm')
				{
					$objFile = new \File($file->path);	
					$strText = $objFile->getContent();
					$isHtml = true;
				}
	        }
			else
			{
				// cannot use contao classes here
				if (!file_exists(TL_ROOT . '/' . $arrHtmlTemplate))
				{
					$isHtml = false;
					break;
				}
				$strText = file_get_contents(TL_ROOT . '/' . $arrHtmlTemplate);
				$isHtml = true;
			}
		}	
			
		// Replace inserttags in text fields
		$strText = $this->replaceInsertTags($strText);
		$strSubject = $this->replaceInsertTags($strSubject);
		
		//-- build mail
		if (version_compare(VERSION, '3.1', '>='))
		{
			$objEmail = new \Email();
			$objEmail->__set('from', implode(',',$arrSenders));
			#$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
			#$objEmail->replyTo = $GLOBALS['TL_ADMIN_EMAIL'];
			$objEmail->subject = $strSubject;			
			
			if($isHtml)
			{
				$objEmail->__set('html',$strText);
			}
			else
			{
				$objEmail->__set('text',$strText);
			}
			
			// Attachments
			if(count($arrAttachments) > 0)
			{
				foreach($arrAttachments as $file)
				{
					$objEmail->attachFile($file);
				}
			}
			
			if($objEmail->sendTo(implode(',', $arrRecipients)))
			{
				return true;
			}
		
			return false;
		}
		
		// ---- Backwards compatibility from here ----
		
		// Include library
        require_once(TL_ROOT.'/'.$GLOBALS['FORM_PDF']['swiftmailer']);
        
        $objMailer = new \Swift_Mailer(new \Swift_MailTransport());
		$objMessage = \Swift_Message::newInstance();
		
		$objMessage->setSubject($strSubject); // Message subject
		
		// To:
		$objMessage->setTo($arrRecipients);
		
		// From:
		$objMessage->setFrom($arrSenders);
		
		// Message
		$objMessage->setBody($strText, 'text/plain'); // 'text/html'
		if($isHtml)
		{
			$objMessage->setBody($strText, 'text/html');
		}
		
		// Attachments
		if(count($arrAttachments) > 0)
		{
			foreach($arrAttachments as $file)
			{
				$filename = basename($file);
				
				$objMessage->attach(Swift_Attachment::fromPath($file)->setFilename($filename));
			}
		}
		
		if ($objMailer->send($objMessage))  
		{    
			return true;
		}
		
		return false;
	}


	/**
	 * Generate a pdf from a html string, save to disk and return the path to the file
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function printPDFtoFile($strHtml,$strPath,$strFilename,$bolIncrement=false)
	{
		// get the pdf object
		$pdf = $this->generatePDF($strHtml,$this->strPlugin);

		if($strPath == '/') {$strPath = '';}

		$file = $strPath.$strFilename;

		if($bolIncrement && file_exists($file.'.pdf'))
		{
			$time = time();
			$file .= '_'.$time;
		}

		// output pdf and save
		switch($this->strPlugin)
		{
			case 'tcpdf':
				$pdf->Output($file.'.pdf', 'F'); // F = save to a local server file with the name given by name.
				break;
			case 'dompdf':
				// imports
	
				// render pdf
				$pdf->render();
				$strBuffer = ltrim($pdf->output(array("compress" => 0)));
				
				// store data local
				$this->pdf_content = $strBuffer;
				
				file_put_contents($file.'.pdf', $strBuffer);
	
				break;
			default:
				// add HOOK for custom rendering plugin routines here
				break;
		}


		return $file.'.pdf';
	}


	/**
	 * Generate a pdf from a html string and send diretely to the browser
	 * @param string
	 * @param string
	 * @param string
	 * @return boolean
	 */
	public function printPDFtoBrowser($strHtml,$strFilename)
	{
		// get the pdf object
		$pdf = $this->generatePDF($strHtml,$this->strPlugin);

		$file = $strFilename;

		// output pdf and send to browser
		switch($this->strPlugin)
		{
			case 'tcpdf':
				$pdf->Output($file.'.pdf', 'D'); // D = send to the browser and force a file download with the name given by name.
				break;
			case 'dompdf':
	
				// render pdf
				$pdf->render();
				$pdf->stream($file.'.pdf', array("Attachment" => 1));
				break;
			default:
				// add HOOK for custom rendering plugin routines here
				break;
		}

		return $file.'.pdf';
	}


	/**
	 * Do the same as the printArticleAsPdf from Controller.php
	 * Generate a pdf file from a html string
	 * @param string
	 * @return object
	 */
	protected function generatePDF($strHtml,$strPlugin='tcpdf')
	{
		//-- prepare content
		$strHtml = $this->replaceInsertTags($strHtml);
		$strHtml = html_entity_decode($strHtml, ENT_QUOTES, $GLOBALS['TL_CONFIG']['characterSet']);
		$strHtml = $this->convertRelativeUrls($strHtml, '', true);

		// Remove form elements and JavaScript links
		$arrSearch = array
		(
			'@<form.*</form>@Us',
			'@<a [^>]*href="[^"]*javascript:[^>]+>.*</a>@Us'
		);

		$strHtml = preg_replace($arrSearch, '', $strHtml);

		// Handle line breaks in preformatted text
		$strHtml = preg_replace_callback('@(<pre.*</pre>)@Us', 'nl2br_callback', $strHtml);

		// Default PDF export using TCPDF
		$arrSearch = array
		(
			'@<span style="text-decoration: ?underline;?">(.*)</span>@Us',
			'@(<img[^>]+>)@',
			'@(<div[^>]+block[^>]+>)@',
			'@[\n\r\t]+@',
			'@<br( /)?><div class="mod_article@',
			'@href="([^"]+)(pdf=[0-9]*(&|&amp;)?)([^"]*)"@'
		);

		$arrReplace = array
		(
			'<u>$1</u>',
			'<br>$1',
			'<br>$1',
			' ',
			'<div class="mod_article',
			'href="$1$4"'
		);

		$strHtml = preg_replace($arrSearch, $arrReplace, $strHtml);
		//--


		//-- switch plugins
		if($strPlugin == 'tcpdf')
		{
			$pdf = $this->generatePDF_TCPDF($strHtml);
		}
		else if($strPlugin == 'dompdf')
		{
			$pdf = $this->generatePDF_DOMPDF($strHtml);
		}
		else
		{
			// add hook here for other plugins
			throw new \Exception('No PDF render plugin selected');
		}
		
		// HOOK: allow individual PDF routines before printing
		if (isset($GLOBALS['TL_HOOKS']['generatePdf']) && count($GLOBALS['TL_HOOKS']['generatePdf']) > 0)
		{
			foreach ($GLOBALS['TL_HOOKS']['generatePdf'] as $callback)
			{
				$this->import($callback[0]);
				$pdf = $this->$callback[0]->$callback[1]($pdf,$strPlguin,$strHtml,$this);
			}
		}


		return $pdf;
	}

	/**
	 * Generate PDF with DOMPDF
	 * @param string
	 * @return object
	 * @copyright: https://github.com/dompdf/dompdf
	 */
	protected function generatePDF_DOMPDF($strHtml)
	{
		// Include library
		require_once(TL_ROOT.'/'.$GLOBALS['FORM_PDF']['dompdf_path'].'/dompdf_config.inc.php');
		if(file_exists(TL_ROOT . '/system/config/dompdf.php'))
		{
			require_once(TL_ROOT . '/system/config/dompdf.php');
		}
		
		// Create new object
		$pdf = new DOMPDF();

		// Set paper size
		$pdf->set_paper( strtolower($this->strPaper) ?: 'a4' );

		// Set path
		$pdf->set_base_path(TL_ROOT);

		// Load html content
		$pdf->load_html($strHtml);

		return $pdf;
	}



	/**
	 * Generate PDF with TCPDF
	 * @param string
	 * @return object
	 */
	protected function generatePDF_TCPDF($strHtml)
	{
		// TCPDF configuration
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_charset'] = $GLOBALS['TL_CONFIG']['characterSet'];
		$l['a_meta_language'] = $GLOBALS['TL_LANGUAGE'];
		$l['w_page'] = 'page';

		// Include library
		require_once(TL_ROOT . '/system/config/tcpdf.php');

        if (version_compare(VERSION, '2.11', '<='))
        {
            require_once(TL_ROOT . '/plugins/tcpdf/tcpdf.php');
            require_once(TL_ROOT . '/plugins/tcpdf/htmlcolors.php');
        }
        elseif (version_compare(VERSION, '2.11', '>') && version_compare(VERSION, '3.1', '<'))
        {
            require_once(TL_ROOT . '/system/vendor/tcpdf/tcpdf.php');
            require_once(TL_ROOT . '/system/vendor/tcpdf/htmlcolors.php');
        }
        elseif (version_compare(VERSION, '3.0', '>')) {
            require_once(TL_ROOT . '/system/modules/core/vendor/tcpdf/tcpdf.php');
            require_once(TL_ROOT . '/system/modules/core/vendor/tcpdf/include/tcpdf_colors.php');
        }
        elseif (version_compare(VERSION, '3.3', '>=')) {
			require_once(TL_ROOT . '/vendor/tecnick.com/tcpdf/tcpdf.php');
		}
		
        else{}

		// Create new PDF document
		
		$paper = $this->strPaper ?: PDF_PAGE_FORMAT;
		
		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $paper, true);

		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		#$pdf->SetTitle($arrSettings['title']);
		#$pdf->SetSubject($arrSettings['title']);
		#$pdf->SetKeywords($arrSettings['keywords']);

		// Prevent font subsetting (huge speed improvement)
		$pdf->setFontSubsetting(false);

		// Remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// Set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

		// Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set some language-dependent strings
		$pdf->setLanguageArray($l);

		// Initialize document and add a page
        if (version_compare(VERSION, '3.1', '<')) {
		    $pdf->AliasNbPages();
        }
        else {
            #$pdf->AliasNbPages();
        }

		$pdf->AddPage();

		// Set font
		$pdf->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN);

		// Line Height
		if(PDF_CELL_HEIGHT_RATIO > 0) {$pdf->setCellHeightRatio(PDF_CELL_HEIGHT_RATIO);}

		// Cell padding
		if(PDF_CELL_PADDING > 0) {$pdf->SetCellPadding(PDF_CELL_PADDING);}

		// Set Font size
		if(PDF_FONT_SIZE > 0){$pdf->SetFontSize(PDF_FONT_SIZE);}


		// Write the HTML content
		$pdf->writeHTML($strHtml, true, 0, true, 0);

		// Close and output PDF document
		$pdf->lastPage();

		// send to browser
		#$pdf->Output(standardize(ampersand($strFilename, false)) . '.pdf', 'D');

		// save file
		#$pdf->Output($strFile, 'F');

		return $pdf;
	}


}