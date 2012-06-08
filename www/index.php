<?php
/* FS2 PHP Init */
set_include_path('.');
define('FS2_ROOT_PATH', './', true);
require_once(FS2_ROOT_PATH . 'includes/phpinit.php');
phpinit();
/* End of FS2 PHP Init */



// Inlcude DB Connection File or exit()
require_once(FS2_ROOT_PATH . 'login.inc.php');

//Include Functions-Files
require_once(FS2_ROOT_PATH . 'includes/cookielogin.php');
require_once(FS2_ROOT_PATH . 'includes/imagefunctions.php');
require_once(FS2_ROOT_PATH . 'includes/indexfunctions.php');

//Include Library-Classes
#require_once(FS2_ROOT_PATH . 'libs/class_HashMapper.php');
#require_once(FS2_ROOT_PATH . 'libs/class_template.php');
#require_once(FS2_ROOT_PATH . 'libs/class_fileaccess.php');
#require_once(FS2_ROOT_PATH . 'libs/class_lang.php');
#require_once(FS2_ROOT_PATH . 'libs/class_search.php');
#require_once(FS2_ROOT_PATH . 'libs/class_searchquery.php');
#require_once(FS2_ROOT_PATH . 'libs/class_Mail.php');
#require_once(FS2_ROOT_PATH . 'libs/class_MailManager.php');


// Load Text TODO: backwards compatibiliy
$TEXT['frontend'] = $FD->getOldTetxt();


// Constructor Calls
// TODO: "Constructor Hook"
get_goto();
setTimezone($FD->cfg('timezone'));
delete_old_randoms();
search_index();
set_style();
copyright();
count_all($FD->cfg('goto'));
save_referer();
save_visitors();


// Get Body-Template
$theTemplate = new template();
$theTemplate->setFile('0_main.tpl');
$theTemplate->load('MAIN');
$theTemplate->tag('content', get_content($FD->cfg('goto')));
$theTemplate->tag('copyright', get_copyright());

$template_general = (string) $theTemplate;
$template_general = tpl_functions_init($template_general);

// TODO: "Template Manipulation Hook"

// Display Page
echo get_maintemplate($template_general);


// Shutdown System
// TODO: "Shutdown Hook"
unset($FD);
?>
