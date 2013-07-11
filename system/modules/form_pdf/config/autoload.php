<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Form_pdf
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'FormPDF' => 'system/modules/form_pdf/FormPDF.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'pdf_example_html'  => 'system/modules/form_pdf/templates',
	'pdf_example_plain' => 'system/modules/form_pdf/templates',
));
