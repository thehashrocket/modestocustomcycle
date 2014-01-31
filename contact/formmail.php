<?php
$FM_VERS = "1.20";		// script version
/* ex:set ts=4 sw=4:
 * FormMail PHP script.  This script requires PHP 4 or later.
 * Copyright (c) 2001-2004 Root Software Pty Ltd.  All rights reserved.
 *
 * Visit us at http://www.tectite.com/
 * for updates and more information.
 *
 **** If you use this FormMail, please support its development and other
 **** freeware products by putting the following link on your website:
 ****	Visit www.tectite.com for free FormMail and <a href="http://www.tectite.com/">copy protection</a> software.
 *
 * Author: Russell Robinson, 2nd October 2001
 * Last Modified: RR 12:32 Mon 19 January 2004
 * QVCS Version: $Revision: 1.20 $
 *
 * Read This First
 * ~~~~~~~~~~~~~~~
 *	This script is very heavily documented!  It looks daunting, but
 *	really isn't.
 *	If you have experience with PHP or other scripting languages,
 *	here's what you *need* to read:
 *		- Features
 *		- Configuration (TARGET_EMAIL)
 *		- Creating Forms
 *	That's it!  (Alternatively, just read the Quick Start section below).
 *
 * Quick Start
 * ~~~~~~~~~~~
 *	1. Edit this file and set TARGET_EMAIL for your requirements (approx
 *		line 466 in this file - replace "yourhost\.com" with your mail server's
 *		name)
 *	2. Install this file as formmail.php on your web server
 *	3. Create an HTML form and:
 *		- specify a hidden field called "recipients" with the email address
 *			of the person to receive the form's results.
 *		- post the form to formmail.php on your web server
 *
 * Purpose:
 * ~~~~~~~~
 *	To accept HTTP POST information from a form and mail it to recipients.
 *	This version can also supply data to a TectiteCRM document, usually
 *	for insertion into the CRM database.
 *
 * What does this PHP script do?
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *	On your web site, you may have one or more HTML forms that accept
 *	information from people visiting your website.  Your aim is for your
 *	website to email that information to you and/or add it to a database.
 *	formmail.php performs those functions.
 *
 * Features
 * ~~~~~~~~
 *		-	Optionally sends email of form results to recipients that
 *			can be specified in the form itself.
 *		-	Optionally stores the form results in a CSV (comma-separated-
 *			values) file on your server.
 *		-	Optionally logs form activity.
 *		-	Optionally sends form results to a TectiteCRM document; generally,
 *			to automatically update the CRM database.
 *		-	Recipient email addresses can be mangled in your forms to
 *			protect them from spambots.
 *		-	Emails can be processed through any program (typically an
 *			encryption program) before sending.
 *		-	Successful processing can redirect the user to any URL.
 *			For example, for downloads, we redirect the user to the file
 *			they want to download.
 *		-	Supports any number of recipients.  For security, recipient
 *			domains must be specified inside the script (see "Configuration"
 *			for details).
 *		-	Failed processing can redirect to a custom URL.
 *		-	Failed processing can be reported to a specific email address.
 *		-	Supports both GET and POST methods of form submission.
 *		-	Provides most of the features of other formmail scripts.
 *
 * Security
 * ~~~~~~~~
 *	Security is the primary concern in accepting data from your website visitors.
 *	formmail.php has several security features designed into it.  Note, however,
 *	it requires configuration for your particular web site.
 *
 * Configuration
 * ~~~~~~~~~~~~~
 *	For instructions on configuring this program, go to the section
 *	titled "CONFIGURATION" (after reading the legal stuff below).
 *	There is only one mandatory setting: TARGET_EMAIL
 *
 * Creating Forms
 * ~~~~~~~~~~~~~~
 *	This section explains how to call formmail.php from your HTML
 *	forms.  You already need to know how to create an HTML form, but
 *	this section will tell you how to link it with this formmail script.
 *
 *	Your form communicates its requirements to formmail.php through
 *	a set of "hidden" fields (using <INPUT TYPE="HIDDEN"...>).  The data to
 *	be processed by formmail (e.g. the actual email to send) comes from
 *	a combination of hidden fields and other form fields (i.e. data entry
 *	fields from the user).
 *
 *	Here are the steps to use formmail.php with your HTML form:
 *		1. Create your HTML form using standard HTML
 *		2. Ensure your form has the following fields defined.  These are
 *		   fields you expect the user to fill in:
 *				email		the user's email address
 *				realname	the real name of the user
 *		3. Add the following hidden fields to your form.  Note that all
 *		   are optional:
 *				recipients	a comma-separated list of email addresses that
 *							the form results will be sent to.  These must
 *							be valid according to the "TARGET_EMAIL" configuration.
 *							Example:
 *								russ.robbo@rootsoftware.com,sales@rootsoftware.com.au
 *				alert_to	email address to send errors/alerts to
 *							Example:
 *								webmaster@rootsoftware.com
 *				required	a list of fields the user must provide, together
 *							with a friendly name.  The field list is separated
 *							by commas, and you append the friendly name to
 *							the field name with a ':'.
 *							Example:
 *								email:Your email address,realname:Your name,Country,Reason:The reason you're interested in our product
 *				good_url	the URL to redirect to on successful processing
 *				bad_url		the URL to redirect to on failed processing
 *				subject		a subject line for the email that's sent to your
 *							recipients
 *							Example:
 *								Form Submission
 *				env_report	a comma-separated list of environment variables
 *							you want included in the email
 *							Example:
 *								REMOTE_HOST,REMOTE_ADDR,HTTP_USER_AGENT,AUTH_TYPE,REMOTE_USER
 *				filter		name of a filter to process the email before sending
 *							You can encode or encrypt the email, for example.
 *				logfile		name of a file to append activity to.  Note that
 *							you must configure "LOGDIR" for this to work.
 *							Example:
 *								formmail.log
 *				csvfile		name of the CSV database to append results to. Note that
 *							you must configure "CSVDIR" for this to work. You
 *							must also specify the csvcolumns field.
 *							Example:
 *								formmail.csv
 *				csvcolumns	comma-separated list of field names you want to
 *							store in the CSV database.  These are the field
 *							names in your form, and the order specifies the
 *							order for storage in the CSV database.
 *							Example:
 *								email,realname,Country,Reason
 *				crm_url		a URL to send the form data to.  This is for use
 *							with the TectiteCRM system.
 *				crm_spec	a specification to pass to TectiteCRM.  Please
 *							read the TectiteCRM documentation for details of
 *							how to use this field.
 *
 *		4. Check that you've provided at least one of these fields:
 *				recipients, or
 *				logfile, or
 *				csvfile and csvcolumns, or
 *				crm_url and crm_spec
 *		   If you don't specify any of these, then formmail.php will fail
 *		   because you've given it no work to do!
 *
 *	Note that we've provided a sample HTML form to get you started.  It's
 *	called "sampleform.htm" and you'll find it at http://www.tectite.com/
 *
 *	Note also that the default success and failure pages shown by formmail.php
 *	are quite basic.  We recommend that you provide your own pages
 *	with "good_url" and "bad_url".
 *
 * Copying and Use
 * ~~~~~~~~~~~~~~~
 *	formmail.php is provided free of charge and may be freely distributed
 *	and used provided that you:
 *		1. keep this header, including copyright and comments,
 *		   in place and unmodified; and,
 *		2. do not charge a fee for distributing it, without an agreement
 *		   in writing with Root Software Pty Ltd allowing you to do so; and,
 *		3. if you modify formmail.php before distributing it, you clearly
 *		   identify:
 *				a) who you are
 *				b) how to contact you
 *				c) what changes you have made
 *				d) why you have made those changes.
 *
 * Warranty and Disclaimer
 * ~~~~~~~~~~~~~~~~~~~~~~~
 *	formmail.php is provided free-of-charge and with ABSOLUTELY NO WARRANTY.
 *	It has not been verified for use in critical applications, including,
 *	but not limited to, medicine, defense, aircraft, space exploration,
 *	or any other potentially dangerous activity.
 *
 *	By using formmail.php you agree to indemnify Root Software Pty Ltd,
 *	its agents, employees, and directors from any liability whatsoever.
 *
 * We still care
 * ~~~~~~~~~~~~~
 *	If you report problems to us, we will respond to your report and make
 *	endeavours to rectify any faults you've detected as soon as possible.
 *	To contact us, visit http://www.tectite.com/contacts.php.
 *
 * Version History
 * ~~~~~~~~~~~~~~~
 *
 **Revision 1.19: 19-Jan-2004
 * Added support for missing environment variables: if an environment variable
 * isn't in the environment, then FormMail looks in the server variables for it.
 *
 * Improved support for different server configurations; if SCRIPT_FILENAME
 * is not available, PATH_TRANSLATED is used; if one or more isn't available
 * no error message is produced (previous version displayed an error message
 * depending on the PHP configuration).
 *
 * Added configuration option for line termination in the email body.
 *
 * The FormMail version is now displayed in the default error page.
 *
 **Revision 1.18: 13-Jan-2004
 * Fixed a problem when mail sending failed; now, FormMail reports the error
 * to the user (by going to bad_url or generating the standard error page)
 * instead of showing success.
 * Added support for GET method of form submission (FormMail automatically
 * detects the method from the PHP Server variables).
 *
 **Revision 1.16 & 1.17: 4-Jan-2004 & 5-Jan-2004
 * Added support for PHP versions before 4.2.3 (i.e. from 4.0.0 onwards).
 *
 **Revision 1.14 & 1.15: 3-Jan-2004
 * Added some more comments.
 *
 **Revision 1.13: 29-Dec-2003
 * Added Quick Start section, some more samples, and some more comments.
 *
 **Revision 1.12: 26-Sep-2003
 * Replaced use of PATH_TRANSLATED with the more reliable SCRIPT_FILENAME.
 *
 **Revision 1.10 & 1.11: 16-Sep-2003
 * Added handling of magic_quotes_gpc setting.
 *
 **Revision 1.9: 5-Sep-2003
 * Added ex/vi initialisation string.
 *
 **Revision 1.8: 9-Jul-2003
 * Added a workaround for a PHP bug: http://bugs.php.net/bug.php?id=21311
 *
 **Revision 1.7: 19-May-2003
 * If a form contains only one non-special field (a "special" field is
 * one of the pre-defined ones, like "email", "realname"), then formmail.php
 * formats the single field as the email to be sent.  This feature allows
 * formmail.php to be used for a simple email interface.
 * Modified some wordings.
 *
 **Revision 1.4: 13-May-2003
 * First released version.
 */

// Check for old version of PHP - die if too old.
function IsOldVersion()
{
    $i_too_old = 3;             // version 3 PHP is not usable here
    $a_modern = array(4,1,0);   // versions prior to this are "old" - "4.1.0"

    $a_this_version = explode(".",phpversion());

    if ((int) $a_this_version[0] <= $i_too_old)
        die("This script requires at least PHP version 4.  Sorry.");
    $i_this_num = ($a_this_version[0] * 10000) +
                    ($a_this_version[1] * 100) +
                    $a_this_version[2];
    $i_modern_num = ($a_modern[0] * 10000) +
                    ($a_modern[1] * 100) +
                    $a_modern[2];
    return ($i_this_num < $i_modern_num);
}

$bUseOldVars = IsOldVersion();
session_start();

    //
    // we set references to the super global vars to handle version differences
    //
if ($bUseOldVars)
{
    $sServerVars = &$HTTP_SERVER_VARS;
	$sSessionVars = &$HTTP_SESSION_VARS;
    $sFormVars = &$HTTP_POST_VARS;
}
else
{
	$sServerVars = &$_SERVER;
	$sSessionVars = &$_SESSION;
	$sFormVars = &$_POST;
}
	//
	// If the form submission was using the GET method, switch to the
	// GET vars instead of the POST vars
	//
if (isset($sServerVars["REQUEST_METHOD"]) && $sServerVars["REQUEST_METHOD"] === "GET")
	if ($bUseOldVars)
		$sFormVars = &$HTTP_GET_VARS;
	else
		$sFormVars = &$_GET;

if (!isset($REAL_DOCUMENT_ROOT))
{
	if (isset($sServerVars['SCRIPT_FILENAME']))
		$REAL_DOCUMENT_ROOT = dirname($sServerVars['SCRIPT_FILENAME']);
	elseif (isset($sServerVars['PATH_TRANSLATED']))
		$REAL_DOCUMENT_ROOT = dirname($sServerVars['PATH_TRANSLATED']);
	else
		$REAL_DOCUMENT_ROOT = "";
}

/*****************************************************************************/
/* CONFIGURATION (do not alter this line in any way!!!)                      */
/*****************************************************************************
 * This is the *only* place where you need to modify things to use formmail.php
 * on your particular system.  This section finishes at "END OF CONFIGURATION".
 *
 * Each variable below is marked as LEAVE, OPTIONAL or MANDATORY.
 * What we mean is:
 *		LEAVE		you can change this if you really want to and know what
 *					you're doing, but we recommend that you leave it unchanged
 *
 *		OPTIONAL	you can change this if you need to, but its current
 *					value is fine and we recommend that you leave it unchanged
 *					unless you need a different value
 *
 *		MANDATORY	you *must* modify this for your system.  The script will
 *					not work if you don't set the value correctly.
 *
 *****************************************************************************/

	//
	// ** LEAVE **
	// EMAIL_NAME is a limited set of characters that you can use for your
	// target email user names (the text before the "@");
	// these are accepted:
	//		russellr
	//		russ.robinson
	//		russ61robbo
	//
	// The pattern we've provided doesn't match every valid user name in a
	// mail address, but, since these email addresses are ones you'll
	// choose and are part of your organisation, the limited set is generally
	// no problem.
	//
	// If you want to use other user names, then you need to change the pattern
	// accordingly.
	//
	// We recommend that you don't modify this pattern, but, rather, use
	// conforming email user names as your target email addresses.
	// BTW, the pattern is processed case-insensitively, so there's
	// no need to provide upper and lower case values.
	//
define("EMAIL_NAME","^[a-z0-9.]+");	// the '^' is an important security feature!

    //
	// ** MANDATORY **
    // Set TARGET_EMAIL to a list of patterns that callers are allowed
    // to send mail to; this is a *critical* security mechanism and
	// prevents relaying.  Relaying is where an unauthorized person uses this
	// script to send mail to *anyone* in the world.
	//
	// By setting TARGET_EMAIL to a set of patterns for your email addresse,
	// then relaying is prevented.
	//
	// More information about TARGET_EMAIL.
	//
	// Instructions
	// ~~~~~~~~~~~~
	//	1.	If you only have one host or domain name:
	//			replace "yourhost" with the name of your email server computer.
	//		For example,
	//			EMAIL_NAME."@yourhost\.com$"
	//		becomes:
	//			EMAIL_NAME."@microsoft\.com$"
	//		If you work for Microsoft (microsoft.com).
	//
	//	2.	If you have a domain name other than ".com":
	//			replace "yourhost\.com" with your email server's full
	//			domain name.
	//		For example,
	//			EMAIL_NAME."@yourhost\.com$"
	//		becomes:
	//			EMAIL_NAME."@apache\.org$"
	//		If you work for the Apache organisation (apache.org).
	//		Another example is:
	//			EMAIL_NAME."@rootsoftware\.com\.au$"
	//		If you work for Root Software in Australia (rootsoftware.com.au).
	//
	//	3.	If you want to accept email to several domains, you can do that too.
	//		Here's an example.  At Root Software, our forms can send to any of
	//		the following domains:
	//			rootsoftware.com
	//			rootsoftware.com.au
	//			ttmaker.com
	//			timetabling.org
	//			timetabling-scheduling.com
	//			tectite.com
	//		To achieve this, we have the following setting:
	//			$TARGET_EMAIL = array(EMAIL_NAME."@rootsoftware\.com$",
	//								EMAIL_NAME."@rootsoftware\.com\.au$",
	//								EMAIL_NAME."@ttmaker\.com$",
	//								EMAIL_NAME."@timetabling\.org$",
	//								EMAIL_NAME."@timetabling-scheduling\.com$",
	//								EMAIL_NAME."@tectite\.com$",
	//								);
	//
	//	4.	If you want to accept email to several specific email addresses,
	//		that's fine too.  Here's an example:
	//			$TARGET_EMAIL = array("russell\.robinson@rootsoftware\.com$",
	//								"info@ttmaker\.com$",
	//								"sales@timetabling\.org$",
	//								"webmaster@timetabling-scheduling\.com$",
	//								);
	//
	// More Instructions
	// ~~~~~~~~~~~~~~~~~
	// TARGET_EMAIL is an array.  This means it can contain many "elements".
	// Each element is a string (a set of characters in quotes).
	// To create many elements, you simply list the strings separated by
	// a comma.
	// For example:
	//		$TARGET_EMAIL = array("String 1","String 2","String 3");
	//
	// You can put a newline after each comma, to make it more readable.
	// Like this:
	//		$TARGET_EMAIL = array("String 1",
	//							  "String 2",
	//							  "String 3");
	//
	// If you look below, you may be wondering why you can see the following:
	//		EMAIL_NAME."@yourhost\.com$"
	// and that's not a string!
	//
	// It's a string concatenation. EMAIL_NAME is a string (and you can
	// see it defined above), and the "." after it says "append the following
	// string to EMAIL_NAME and make one larger string".
	//
	// So,
	//		EMAIL_NAME."@yourhost\.com$"
	// becomes the string:
	//		"^[a-z0-9.]+@yourhost\.com$"
	//
	// What are all the \ ^ $ and other punctuation characters?
	//
	// The strings we're defining contain "patterns".  We won't go into
	// patterns here (it's a large subject), but we will explain a few
	// important things:
	//	^	means the beginning; we want email user names to match only
	//		at the beginning of the input, so that's why EMAIL_NAME starts
	//		with ^
	//	.	matches any single character
	//	\	stops the following character from being a pattern matcher
	//	$	matches the end
	//
	// So, when we want to match ".com", we need to say "\.com".  Otherwise,
	// ".com" would match "Xcom", "Ycom", "xcom", etc., as well as ".com".
	// The "\." says match only ".".
	//
	// Also, if your server is "yourhost.com", you don't want to match
	// "yourhost.com.anythingelse", so we put "yourhost\.com$" to match
	// the end.
    //
$TARGET_EMAIL = array(EMAIL_NAME."@sbcglobal\.net$");

	//
	// ** OPTIONAL **
	// Set AT_MANGLE to a string to replace with "@".  To disable this
	// feature, set to empty string.
	//
	// If you enable this feature, you're protecting your email addresses
	// you specify on your forms from SpamBots.
	//
	// SpamBots are programs that search for email addresses on the
	// Internet.  Typically, they look for "mailto:someone@somewhere".
	//
	// However, email addresses you specify in your forms will be like
	// this:
	//	<input type="hidden" name="recipients" value="someone@yourhost.com">
	//
	// It is possible that some SpamBots will find your email addresses hidden
	// in your forms.
	//
	// The AT_MATCH feature allows you to mangle your email addresses and
	// protect them from SpamBots.
	//
	// Here's an example:
	//			define("AT_MANGLE","_*_");
	//
	// This tells formmail.php to replace "_*_" in your email address with "@".
	// So, in your forms you can specify:
	//	<input type="hidden" name="recipients" value="someone_*_yourhost.com">
	//
	// No SpamBot will recognize this as an email address, and your addresses
	// will be safe!
	//
	// If you use this feature, we encourage you to be creative and different
	// from everyone else.
	//
	// Here are some more examples:
	//			define("AT_MANGLE","_@_");		// e.g. john_@_yourhost.com
	//											// SpamBots may recognize this,
	//											// but it'll be an invalid address
	//			define("AT_MANGLE","AT");		// e.g. johnATyourhost.com
	//
	// Note that the AT_MANGLE pattern match is case-sensitive, so "AT" is
	// different from "at".
	//
define("AT_MANGLE","");

	//
	// ** LEAVE **
	// HEAD_CRLF is the line termination for email header lines.  The email
	// standard (RFC-822) specifies line termination should be CR plus LF.
	//
	// Many mail systems will work with just LF and some are reported to
	// actually fail if the email conforms to the standard (with CR+LF).
	//
	// If you have special requirements you can change HEAD_CRLF to another
	// string (such as "\n" to just get LF (line feed)), but be warned that
	// this make break the email standard.
	//
	// Note: this value is not currently used in this script.  It is here
	// for documentation and future use only.
	//
define("HEAD_CRLF","\r\n");

	//
	// ** OPTIONAL **
	// BODY_LF is the line termination for email body lines.  The email
	// standard (RFC-822) does not clearly line termination for the body
	// of emails; the body doesn't have to have any "lines" at all.  However,
	// it does allow CR+LF between sections of text in the body.
	//
	// Most mail systems will work with just LF and that's the default
	// for this FormMail.
	//
	// If you want your email bodies to be line terminated differently, you
	// can specify a different value below.
	//
define("BODY_LF","\n");		// the default: just LF
//define("BODY_LF","\r\n");		// use this for CR+LF

	//
	// ** OPTIONAL **
	// Set DEF_ALERT to the email address that will be sent any alert
	// messages (such as errors) from the script.  This value is
	// only used if the 'alert_to' is not provided by the form.
	// If neither alert_to nor DEF_ALERT are provided, no alerts are sent.
	//
	// Note also, the domain for this email address must be included
	// in TARGET_EMAIL.
	// Example:
	//		webmaster@yourhost.com
	//
define("DEF_ALERT","");

	//
	// ** OPTIONAL **
	// Set FROM_USER to the email address that will be the sender
	// of alert/error messages.  If not specified (comment it out),
	// formmail.php uses "FormMail@SERVER" where SERVER is determined
	// from your web server. If set to "NONE", then no sender is specified.
	//
//$FROM_USER = "formmail@yourhost.com";		// this line is commented out, by default
//$FROM_USER = "NONE";						// use this to show no sender

	//
	// ** OPTIONAL **
	// Set LOGDIR to the directory on your server where log files are
	// stored.  When the form proveds a 'logfile' value, formmail.php
	// expects the file to be in this directory.
	// Generally you want this to be outside your server's WWW directory.
 	// For example, if your server's root (WWW) directory is:
	//			/home/yourname/www
	// use a directory like
	//			/home/yourname/logs
	//
	// If you don't want to support log files, make this an empty string:
	//		$LOGDIR = "";
	//
$LOGDIR = "";							// directory for log files; empty string to
										// disallow log files
	//
	// ** OPTIONAL **
	// Set CSVDIR to the directory on your server where CSV files are
	// stored.  When the form proveds a 'csvfile' value, formmail.php
	// expects the file to be in this directory.
	// Generally you want this to be outside your server's WWW directory.
 	// For example, if your server's root (WWW) directory is:
	//			/home/yourname/www
	// use a directory like
	//			/home/yourname/csv
	//
	// If you don't want to support CSV files, make this an empty string:
	//		$CSVDIR = "";
	//
$CSVDIR = "";						// directory for csv files; empty string to
									// disallow csv files

	//
	// ** OPTIONAL **
	// Set LIMITED_IMPORT to false if your target database understands
	// escaped quotes and newlines within CSV files.
	//
	// When formmail.php is instructed to write to a CSV file, it
	// can strip special encodings or leave them intact.
	//
	// What you want to do depends on the final destination of your
	// CSV file.  If you intend to import the CSV file into a database,
	// and the database doesn't accept these special encodings, you
	// must leave LIMITED_IMPORT set to true.
	//
	// Microsoft Access is one example of a database that doesn't
	// understand escaped quotes and newlines, so you need LIMITED_IMPORT
	// set to true.
	//
	// When LIMITED_IMPORT is true, the following transformations are made
	// on every form value before placement in the CSV file:
	//		\\		is replaced by 		\
	//		\X		is replaced by		X	where X is any character except \
	//		"		is replaced by		'
	// plus
	//		carriage return characters are removed entirely
	//		line feed characters are replaced with a space character
	//
define("LIMITED_IMPORT",true);		// set to true if your database cannot
									// handle escaped quotes or newlines within
									// imported data.  Microsoft Access is one
									// example.

	//
	// ** OPTIONAL **
	// Set VALID_ENV to the enviroment variables the script is allowed to
	// report.  No need to change.
	//
$VALID_ENV = array('HTTP_REFERER','REMOTE_HOST','REMOTE_ADDR','REMOTE_USER',
				'HTTP_USER_AGENT');

	//
	// ** LEAVE **
	// Set DB_SEE_INPUT to true for debugging purposes only.  If set to
	// true the script does nothing except generate a page showing you what
	// it will do.
	//
define("DB_SEE_INPUT",false);		// set to true to just see the input values

	//
	// ** OPTIONAL **
	// Set MAXSTRING to limit the maximum length of any value accepted
	// from the form.
	//
define("MAXSTRING",1024);         	// maximum string length for a value

	//
	// ** OPTIONAL **
	// Set FILTERS to the filter programs you want to support.
	// A filter program is used to process the data before sending in email.
	// For example, an encryption program can be used to encrypt the mail.
	// Note that formmail.php chdir's to the directory of the filter program before
	// running the filter.
	// The format for each filter program is:
	//		"name"=>"program path"
	// Here's an example:
	//		$FILTERS = array("capture"=>"$REAL_DOCUMENT_ROOT/cgi-bin/capcode");
	// This says that when the form specifies a 'filter' value of
	// "capture", run the email through this program:
	//		$REAL_DOCUMENT_ROOT/cgi-bin/capcode
	// You can use the special variable $REAL_DOCUMENT_ROOT to refer
	// to the top of your web server directory.
	// The program can also be outside of the web server directory, e.g.:
	//		/home/yourname/bin/capcode
	//
$FILTERS = array();

/*****************************************************************************/
/* END OF CONFIGURATION (do not alter this line in any way!!!)               */
/*****************************************************************************/

$sSessionVars["FormError"] = NULL;
unset($sSessionVars["FormError"]);          		// start with no error

//
// Note that HTTP_REFERER is easily spoofed, so there's no point in
// using it for security.
//

    //
    // SPECIAL_FIELDS is the list of fields that formmail.php looks for
    //
$SPECIAL_FIELDS = array(
		"email",   		// email address of the person who filled in the form
        "realname", 	// the real name of the person who filled in the form
        "recipients",   // comma-separated list of email addresses to which we'll send the results
        "required",     // comma-separated list of fields that must be found in the input
        "good_url",     // URL to go to on success
        "bad_url",      // URL to go to on error
        "subject",      // subject for the email
        "env_report",   // comma-separated list of environment variables to report
		"filter",		// a support filter to use
		"logfile",		// log file to write to
		"csvfile",		// file to write CSV records to
		"csvcolumns",	// columns to save in the csvfile
		"crm_url",		// URL for sending data to the CRM
		"crm_spec",		// CRM specification (field mapping)
        "alert_to");    // email address to send alerts (errors) to

    //
    // SPECIAL_VALUES is set to the value of the fields we've found
    //  usage: $SPECIAL_VALUES["email"] is the value of the email field
    //
$SPECIAL_VALUES = array();
 	//
	// initialise $SPECIAL_VALUES so that we don't fail on using unset values
	//
foreach ($SPECIAL_FIELDS as $sFieldName)
	$SPECIAL_VALUES[$sFieldName] = "";

    //
    // FORMATTED_INPUT contains the input variables formatted nicely
    //
$FORMATTED_INPUT = array();

	//
	// UnMangle and email address
	//
function UnMangle($email)
{
	if (AT_MANGLE != "")
		$email = str_replace(AT_MANGLE,"@",$email);
	return ($email);
}
    //
    // Check a list of email address (comma separated); returns a list
    // of valid email addresses (comma separated).
    // The return value is true if there is at least one valid email address.
    //
function CheckEmailAddress($addr,&$valid)
{
    global  $TARGET_EMAIL;

    $valid = "";
    $list = explode(",",$addr);
    for ($ii = 0 ; $ii < count($list) ; $ii++)
    {
		$email = UnMangle($list[$ii]);
        for ($jj = 0 ; $jj < count($TARGET_EMAIL) ; $jj++)
            if (eregi($TARGET_EMAIL[$jj],$email))
            {
                if (empty($valid))
                	$valid = $email;
                else
                    $valid .= ",".$email;
            }
    }
    return (!empty($valid));
}

    //
    // Redirect to another URL
    //
function Redirect($url)
{
    header("Location: $url");
    exit;
}

    //
    // Send an email
    //
function SendCheckedMail($to,$subject,$mesg,$headers = "")
{
   	return (mail($to,$subject,$mesg,$headers));
}

    //
    // Send an email
    //
function SendMail($to,$subject,$mesg,$headers = "")
{
    if (CheckEmailAddress($to,$valid))
   		return (SendCheckedMail($valid,$subject,$mesg,$headers));
    return (false);
}

    //
    // Send an alert email
    //
function SendAlert($error)
{
    global  $SPECIAL_VALUES,$FORMATTED_INPUT,$FROM_USER,$sServerVars;

    $alert_to = $SPECIAL_VALUES["alert_to"];
    if (empty($alert_to))
        $alert_to = DEF_ALERT;
    if (!empty($alert_to))
    {
		$from = $headers = "";
		if (isset($FROM_USER))
		{
			if ($FROM_USER != "NONE")
				$headers = $from = "From: $FROM_USER";
		}
		else
			$headers = $from = "From: FormMail@".$sServerVars['SERVER_NAME'];
		$mesg = "To: ".UnMangle($alert_to).BODY_LF;
		if (!empty($from))
			$mesg .= $from.BODY_LF;
		$mesg .= BODY_LF;
        $mesg .= "This error occurred in the script: $error".BODY_LF;
        $mesg .= implode(BODY_LF,$FORMATTED_INPUT);
        SendMail($alert_to,"FormMail script error",$mesg,$headers);
    }
}

    //
    // Report an error
    //
function Error($error_code,$error_mesg,$show = true,$int_mesg = "")
{
    global  $SPECIAL_VALUES,$sSessionVars;

		//
		// Testing with PHP 4.0.6 indicates that this doesn't work; the
		// session variable is not set.  So, we'll also add the
		// error message to the URL.
		//
	$sSessionVars["FormError"] = $error_mesg;
	SendAlert($error_code."\n *****".$int_mesg."*****\nError=".$error_mesg."\n");
    if ($show)
    {
       	$bad_url = $SPECIAL_VALUES["bad_url"];
        if (!empty($bad_url))
		{
			if (strpos($bad_url,'?') === false)
				$bad_url .= '?error='.urlencode("$error_mesg");
			else
				$bad_url .= '&error='.urlencode("$error_mesg");
            Redirect($bad_url);
		}
        else
        {
            $text  = "An error occurred while processing the form.\n";
            $text .= "The error was: $error_mesg";
            CreatePage($text);
        }
    }
    exit;
}

	//
	// Create a simple page with the given text.
    //
function CreatePage($text)
{
	global	$FM_VERS;

	echo "<html>";
	echo "<head>";
	echo "</head>";
	echo "<body>";
	echo nl2br($text);
	echo "<p><p><small>Your form submission was processed by formmail.php ($FM_VERS), available from <a href=\"http://www.tectite.com/\">www.tectite.com</a></small>";
	echo "</body>";
	echo "</html>";
}

	//
	// Strip slashes if magic_quotes_gpc is set.
	//
function StripGPC($s_value)
{
	if (get_magic_quotes_gpc() != 0)
		$s_value = stripslashes($s_value);
	return ($s_value);
}

	//
	// return an array, stripped of slashes if magic_quotes_gpc is set
	//
function StripGPCArray($arr)
{
	if (get_magic_quotes_gpc() != 0)
		foreach ($arr as $key=>$value)
			if (is_string($value))
				$arr[$key] = stripslashes($value);
	return ($arr);
}

	//
	// Strip a value of unwanted characters, which might be hacks.
	//
function Strip($value)
{
	$value = StripGPC($value);
	$value = str_replace("\"","'",$value);
	$value = preg_replace('/[[:cntrl:][:space:]]+/'," ",$value);	// zap all control chars and multiple blanks
	return ($value);
}

	//
	// Parse the input variables and produce textual output from them.
	// Also return non-special values in the given $a_values array.
	//
function ParseInput($vars,&$a_values,$s_line_feed)
{
    global  $SPECIAL_FIELDS,$SPECIAL_VALUES,$FORMATTED_INPUT;

    $output = "";
        //
        // scan the array of values passed in (name-value pairs) and
        // produce slightly formatted (not HTML) textual output
        //
    while (list($name,$raw_value) = each($vars))
    {
        if (is_string($raw_value))
                //
                // truncate the string
                //
        	$raw_value = substr($raw_value,0,MAXSTRING);
        $value = trim(Strip($raw_value));
        if (in_array($name,$SPECIAL_FIELDS))
            $SPECIAL_VALUES[$name] = $value;
		else
		{
			$a_values[$name] = $raw_value;
        	$output .= "$name: $value".$s_line_feed;
		}
        array_push($FORMATTED_INPUT,"$name: '$value'");
    }
    return ($output);
}

    //
    // Get the URL for sending to the CRM.
    //
function GetCRMURL($spec,$vars,$url)
{
    $bad = false;
    $list = explode(",",$spec);
	$map = array();
    for ($ii = 0 ; $ii < count($list) ; $ii++)
    {
        $name = $list[$ii];
        if ($name)
        {
                //
                // the specification must be in this format:
                //      form-field-name:CRM-field-name
                //
            if (($i_crm_name_pos = strpos($name,":")) > 0)
            {
                $s_crm_name = substr($name,$i_crm_name_pos + 1);
                $name = substr($name,0,$i_crm_name_pos);
				if (isset($vars[$name]))
				{
					$map[] = $s_crm_name."=".urlencode($vars[$name]);
					$map[] = "Orig_".$s_crm_name."=".urlencode($name);
				}
            }
			else
			{
					//
					// not the right format, so just include as a parameter
					// check for name=value format to choose encoding
					//
				$a_values = explode("=",$name);
				if (count($a_values) > 1)
					$map[] = urlencode($a_values[0])."=".urlencode($a_values[1]);
				else
					$map[] = urlencode($a_values[0]);
			}
        }
    }
	if (count($map) == 0)
		return ("");
	if (strpos($url,'?') === false)	// append ? if not present
		$url .= '?';
	return ($url.implode("&",$map));
}

	//
	// strip the HTML from a string
	//
function StripHTML($s_str,$s_line_feed = "\n")
{
		//
		// strip HTML comments (s option means include new lines in matches)
		//
	$s_str = preg_replace('/<!--([^-]*([^-]|-([^-]|-[^>])))*-->/s','',$s_str);
		//
		// strip any scripts (i option means case-insensitive)
		//
	$s_str = preg_replace('/<script[^>]*?>.*?<\/script[^>]*?>/si','',$s_str);
		//
		// replace paragraphs with new lines (line feeds)
		//
	$s_str = preg_replace('/<p[^>]*?>/i',$s_line_feed,$s_str);
		//
		// replace breaks with new lines (line feeds)
		//
	$s_str = preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/i',$s_line_feed,$s_str);
		//
		// overcome this bug: http://bugs.php.net/bug.php?id=21311
		//
	$s_str = preg_replace('/<![^>]*>/s','',$s_str);
		//
		// get rid of all HTML tags
		//
	$s_str = strip_tags($s_str);
	return ($s_str);
}
	//
	// open the given URL to send data to it, we expect the response
	// to contain at least __OK__= followed by true or false
	//
function SendToCRM($s_url)
{
@	$fp = fopen($s_url,"r");
	if (!$fp)
	{
		SendAlert("Failed to open CRM URL '$s_url'");
		return;
	}
	$s_mesg = "";
	while (!feof($fp))
	{
		$s_line = fgets($fp,4096);
		$s_mesg .= $s_line;
	}
	$s_mesg = StripHTML($s_mesg);
	$s_result = preg_match('/__OK__=(.*)/',$s_mesg,$a_matches);
	if (count($a_matches) < 2 || strtolower($a_matches[1]) !== "true")
		SendAlert("SendToCRM failed (url='$s_url'): '$s_mesg'");
	fclose($fp);
}

    //
    // Check the input for required values.  The list of required fields
    // is a comma-separated list of field names.
    //
function CheckRequired($reqd,$vars,&$missing)
{
    $bad = false;
    $list = explode(",",$reqd);
    for ($ii = 0 ; $ii < count($list) ; $ii++)
    {
        $name = $list[$ii];
        if ($name)
        {
                //
                // field names can be just straight names, or in this
                // format:
                //      fieldname:Nice printable name for displaying
                //
            if (($nice_name_pos = strpos($name,":")) > 0)
            {
                $nice_name = substr($name,$nice_name_pos + 1);
                $name = substr($name,0,$nice_name_pos);
            }
            else
                $nice_name = $name;
            if (!isset($vars[$name]) || empty($vars[$name]))
            {
                $bad = true;
                $missing .= "$nice_name\n";
            }
        }
    }
    return (!$bad);
}

    //
    // Return a formatted list of the given environment variables.
    //
function GetEnvVars($list,$s_line_feed)
{
	global	$VALID_ENV,$sServerVars;

    $output = "";
    for ($ii = 0 ; $ii < count($list) ; $ii++)
	{
	    $name = $list[$ii];
		if ($name && array_search($name,$VALID_ENV,true) !== false)
		{
				//
				// if the environment variable is empty or non-existent, try
				// looking for the value in the server vars.
				//
			if (($s_value = getenv($name)) === "" || $s_value === false)
				if (isset($sServerVars[$name]))
					$s_value = $sServerVars[$name];
				else
					$s_value = "";
		    $output .= $name."=".$s_value.$s_line_feed;
		}
	}
    return ($output);
}
	//
	// run data through a supported filter
	//
function Filter($filter,$data)
{
  	global	$FILTERS;

		//
		// get the program name from the filter name
		//
	if (!isset($FILTERS[$filter]) || $FILTERS[$filter] == "")
	{
   		Error("bad_filter","The form has an internal error - unknown filter");
		exit;
	}
	$prog = $FILTERS[$filter];
		//
		// change to the directory that contains the filter program
		//
	$dirname = dirname($prog);
	if (!chdir($dirname))
	{
   		Error("chdir_filter","The form has an internal error - cannot chdir to run filter");
		exit;
	}
		//
		// the output of the filter goes to a temporary file
		//
	$temp_file = tempnam("/tmp","FMF");
	$cmd = "$prog > $temp_file 2>&1";
		//
		// start the filter
		//
	$pipe = popen($cmd,"w");
	if (!$pipe)
	{
	    $err = join('',file($temp_file));
	    unlink($temp_file);
   		Error("filter_not_found","The form has an internal error - cannot execute filter",
						true,$err);
		exit;
	}
		//
		// write the data to the filter
		//
	fwrite($pipe,$data);
	if (pclose($pipe) != 0)
	{
	    $err = join('',file($temp_file));
	    unlink($temp_file);
   		Error("filter_failed","The form has an internal error - filter failed",
						true,$err);
		exit;
	}
		//
		// read in the filter's output and return as the data to be sent
		//
	$data = join('',file($temp_file));
	unlink($temp_file);
	return ($data);
}
	
    //
    // send the given results to the given email addresses
    //
function SendResults($results,$to,$a_values)
{
    global  $SPECIAL_VALUES;

	$b_got_filter = (isset($SPECIAL_VALUES["filter"]) && !empty($SPECIAL_VALUES["filter"]));
		//
		// special case: if there is only one non-special value and no
		// filter, then format it as an email
		//
	if (count($a_values) == 1 && !$b_got_filter)
	{
			//
			// create a new results value
			//
		$results = "";
		foreach ($a_values as $s_value)
		{
				//
				// replace carriage return/linefeeds with <br>
				//
			$s_value = str_replace("\r\n",'<br>',$s_value);
				//
				// replace lone linefeeds with <br>
				//
			$s_value = str_replace("\n",'<br>',$s_value);
				//
				// remove lone carriage returns
				//
			$s_value = str_replace("\r","",$s_value);
				//
				// replace all control chars with <br>
				//
			$s_value = preg_replace('/[[:cntrl:]]+/','<br>',$s_value);
				//
				// strip HTML
				//
			$s_value = StripHTML($s_value,BODY_LF);
			$results .= $s_value;
		}
	}
	else
	{
			//
			// write some standard mail headers - if we're using capcode, these
			// headers are not used, but they are nice to have as clear text anyway
			//
		$res_hdr = "To: $to".BODY_LF;
		$res_hdr .= "From: ".$SPECIAL_VALUES["email"]." (".$SPECIAL_VALUES["realname"].")".BODY_LF;
		$res_hdr .= BODY_LF;
		$res_hdr .= "--START--".BODY_LF;		// signals the beginning of the text to encode

			//
			// put the realname and the email address at the top of the results
			//
		$results = "realname: ".$SPECIAL_VALUES["realname"].BODY_LF.$results;
		$results = "email: ".$SPECIAL_VALUES["email"].BODY_LF.$results;

			//
			// prepend the header to the results
			//
		$results = $res_hdr.$results;

			//
			// if there is a filter required, filter the data first
			//
		if ($b_got_filter)
			$results = Filter($SPECIAL_VALUES["filter"],$results);
	}
		//
		// append the environment variables report
		//
	if (isset($SPECIAL_VALUES["env_report"]))
	{
		$results .= BODY_LF."==================================".BODY_LF;
		$results .= BODY_LF.GetEnvVars(explode(",",$SPECIAL_VALUES["env_report"]),BODY_LF);
	}
		//
		// create the From address as the mail header
		//
	$headers = "From: ".$SPECIAL_VALUES["email"]." (".$SPECIAL_VALUES["realname"].")";
		//
		// send the mail - assumes the to addresses have already been checked
		//
    return (SendCheckedMail($to,$SPECIAL_VALUES["subject"],$results,$headers));
}

    //
    // append an entry to a log file
    //
function WriteLog($log_file)
{
    global  $SPECIAL_VALUES;

@	$log_fp = fopen($log_file,"a");

	if (!$log_fp)
		return;
	$date = gmdate("H:i:s d-M-y T");
	$entry = $date.":".$SPECIAL_VALUES["email"].",".
			$SPECIAL_VALUES["realname"].",".$SPECIAL_VALUES["subject"]."\n";
	fwrite($log_fp,$entry);
	fclose($log_fp);
}

	//
	// write the data to a comma-separated-values file
	//
function WriteCSVFile($csv_file,$vars)
{
    global  $SPECIAL_FIELDS,$SPECIAL_VALUES;

		//
		// create an array of column values in the order specified
		// in $SPECIAL_VALUES["csvcolumns"]
		//
	$column_list = $SPECIAL_VALUES["csvcolumns"];
	if (!isset($column_list) || empty($column_list) || !is_string($column_list))
		return;
	if (!isset($csv_file) || empty($csv_file) || !is_string($csv_file))
		return;

@	$fp = fopen($csv_file,"a");
	if (!$fp)
		return;

	$column_list = explode(",",$column_list);
	$n_columns = count($column_list);

	if (filesize($csv_file) == 0)
	{
		for ($ii = 0 ; $ii < $n_columns ; $ii++)
		{
			fwrite($fp,"\"".$column_list[$ii]."\"");
			if ($ii < $n_columns-1)
				fwrite($fp,",");
		}
		fwrite($fp,"\n");
	}

//	$debug = "";
//	$debug .= "gpc -> ".get_magic_quotes_gpc()."\n";
//	$debug .= "runtime -> ".get_magic_quotes_runtime()."\n";
	for ($ii = 0 ; $ii < $n_columns ; $ii++)
	{
		$value = $vars[$column_list[$ii]];
        if (is_string($value))
                //
                // truncate the string
                //
        	$value = substr($value,0,MAXSTRING);
		$value = trim($value);
		if (LIMITED_IMPORT)
		{
				//
				// the target database doesn't understand escapes, so
				// remove slashes, and double quotes, and newlines
				//
			$value = Strip($value);
		}
//		$debug .= $column_list[$ii]." => ".$value."\n";
		fwrite($fp,"\"".$value."\"");
		if ($ii < $n_columns-1)
			fwrite($fp,",");
	}
	fwrite($fp,"\n");
	fclose($fp);
//	CreatePage($debug);
//	exit;
}

/*
 * The main logic starts here....
 */
$aValues = array();
$Output = ParseInput($sFormVars,$aValues,BODY_LF);
$aFormData = StripGPCArray($sFormVars);
$bDoneSomething = false;
if (!empty($CSVDIR) && isset($SPECIAL_VALUES["csvfile"]) &&
						!empty($SPECIAL_VALUES["csvfile"]))
{
	WriteCSVFile($CSVDIR."/".basename($SPECIAL_VALUES["csvfile"]),$aFormData);
	$bDoneSomething = true;
}
if (DB_SEE_INPUT)
{
	CreatePage(implode("\n",$FORMATTED_INPUT));
    exit;
}
if (!empty($LOGDIR) && isset($SPECIAL_VALUES["logfile"]) && !empty($SPECIAL_VALUES["logfile"]))
{
	WriteLog($LOGDIR."/".basename($SPECIAL_VALUES["logfile"]));
	$bDoneSomething = true;
}
if (isset($SPECIAL_VALUES["crm_url"]) && isset($SPECIAL_VALUES["crm_spec"]))
{
	$sCRM = GetCRMURL($SPECIAL_VALUES["crm_spec"],$aFormData,$SPECIAL_VALUES["crm_url"]);
	if (!empty($sCRM))
	{
		SendToCRM($sCRM);
		$bDoneSomething = true;
	}
}
if (!isset($SPECIAL_VALUES["recipients"]) || empty($SPECIAL_VALUES["recipients"]))
{
		// don't email anyone...
	if (!$bDoneSomething)
	    Error("no_recipients","The form has an internal error - no actions or recipients were specified.");
}
else
{
	if (!CheckEmailAddress($SPECIAL_VALUES["recipients"],$valid_email))
		Error("no_valid_recipients","The form has an internal error - no valid recipients were specified.");
	else
	{
		if (!CheckRequired($SPECIAL_VALUES["required"],$aFormData,$missing))
			Error("missing_fields","The form required some values that you did not seem to provide.\n".$missing);
		elseif (!SendResults($Output,$valid_email,$aValues))
			Error("mail_failed","Failed to send email");
	}
}
$good_url = $SPECIAL_VALUES["good_url"];
if (!isset($good_url) || empty($good_url))
	CreatePage("Thanks!  We've received your information and, if it's appropriate, we'll be in contact with you soon.");
else
	Redirect($good_url);
?>
