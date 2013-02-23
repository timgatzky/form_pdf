-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the Contao    *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_form`
-- 

CREATE TABLE `tl_form` (
  `form_pdf` char(1) NOT NULL default '0',
  `form_pdf_template` varchar(64) NOT NULL default '',
  `form_pdf_attachment` char(1) NOT NULL default '0',
  `form_pdf_plugin` varchar(64) NOT NULL default '',
  `form_pdf_confirmation` char(1) NOT NULL default '0',
  `form_pdf_template_confirmation` varchar(64) NOT NULL default '',
  `form_pdf_attachment_confirmation` char(1) NOT NULL default '0',
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_form_field`
-- 

CREATE TABLE `tl_form_field` (
  `pdf_hide` char(1) NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;