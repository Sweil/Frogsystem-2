<?php
//////////////////////////////////
//// create content container ////
//////////////////////////////////

function get_content_container ( $TOP_TEXT, $CONTENT_TEXT, $OVERALL_STYLE = "width:100%;", $TOP_STYLE = FALSE, $CONTENT_STYLE = FALSE )
{
    $overall_width = ( $OVERALL_STYLE === FALSE ? '' : ' style="'.$OVERALL_STYLE.'"' );
    $top_style = ( $TOP_STYLE === FALSE ? '' : ' style="'.$TOP_STYLE.'"' );
    $content_style = ( $CONTENT_STYLE === FALSE ? '' : ' style="'.$CONTENT_STYLE.'"' );

    $template = '
                <div'.$overall_width.'>
                    <div class="cs_overall">
                        <div class="cs_top_loop">
                            <div class="cs_top_left">
                                <div class="cs_top_right">
                                    <div class="cs_text_top"'.$top_style.'>
                                        '.$TOP_TEXT.'
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cs_mid_loop">
                            <div class="cs_mid_left">
                                <div class="cs_mid_right">
                                    <div class="cs_text_content"'.$content_style.'>
                                        '.$CONTENT_TEXT.'
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cs_bot_loop">
                            <div class="cs_bot_left">
                                <div class="cs_bot_right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    ';

    return $template;
}


/////////////////////////
//// Get Yes/NO Table////
/////////////////////////

function get_yesno_table ( $NAME )
{
        global $admin_phrases;

        return '
                                                                    <table width="100%" cellpadding="4" cellspacing="0">
                                                                                <tr class="bottom pointer" id="tr_yes"
                                                                                        onmouseover="'.color_list_entry ( "del_yes", "#EEEEEE", "#64DC6A", "this" ).'"
                                                                                        onmouseout="'.color_list_entry ( "del_yes", "transparent", "#49C24f", "this" ).'"
                                                                                        onclick="'.color_click_entry ( "del_yes", "#EEEEEE", "#64DC6A", "this", TRUE ).'"
                                                                                >
                                                                                        <td>
                                                                                                <input class="pointer" type="radio" name="'.$NAME.'" id="del_yes" value="1"
                                                    onclick="'.color_click_entry ( "this", "#EEEEEE", "#64DC6A", "tr_yes", TRUE ).'"
                                                                                                >
                                                                                        </td>
                                                                                        <td class="config middle">
                                                                                                '.$admin_phrases[common][yes].'
                                                                                        </td>
                                                                                </tr>
                                                                                <tr class="bottom red pointer" id="tr_no"
                                                                                        onmouseover="'.color_list_entry ( "del_no", "#EEEEEE", "#DE5B5B", "this" ).'"
                                                                                        onmouseout="'.color_list_entry ( "del_no", "transparent", "#C24949", "this" ).'"
                                                                                        onclick="'.color_click_entry ( "del_no", "#EEEEEE", "#DE5B5B", "this", TRUE ).'"
                                                                                >
                                                                                        <td>
                                                                                                <input class="pointer" type="radio" name="'.$NAME.'" id="del_no" value="0" checked
                                                    onclick="'.color_click_entry ( "this", "#EEEEEE", "#DE5B5B", "tr_no", TRUE ).'"
                                                                                                >
                                                                                        </td>
                                                                                        <td class="config middle">
                                                                                                '.$admin_phrases[common][no].'
                                                                                        </td>
                                                                                </tr>
                                                                                '.color_pre_selected ( "del_no", "tr_no" ).'
                                                                        </table>
        ';
}

//////////////////////////////////////
//// Noscript - Do not use hidden ////
//////////////////////////////////////

function noscript_nohidden ()
{
        return '
                <noscript>
                        <style type="text/css">
                                .hidden {display: table-row;}
                        </style>
                </noscript>
        ';
}

////////////////////////////////////////////
//// add zero to figures lower than 10  ////
////////////////////////////////////////////

function add_zero ( $FIGURE )
{
    settype ( $FIGURE, "integer" );
    if ( $FIGURE >= 0 && $FIGURE < 10 ) {
        $FIGURE = "0".$FIGURE;
    }
        return $FIGURE;
}

//////////////////////////
//// Get Article URLs ////
//////////////////////////

function get_article_urls ()
{
    global $global_config_arr;
    global $db;

        $index = mysql_query ( "
                                                        SELECT
                                                                article_url
                                                        FROM
                                                                ".$global_config_arr['pref']."articles
        ", $db );

        while ( $result = mysql_fetch_assoc ( $index ) ) {
                if ( $result['article_url'] != "" ) {
                        $url_arr[] = $result['article_url'];
                }
        }

        return $url_arr;
}

//////////////////////////////
//// Color List Functions ////
//////////////////////////////

function color_list_entry ( $CHECK_ID, $DEFAULTCOLOR, $CHECKEDCOLOR, $ELEMENT_ID )
{
        if ( $CHECK_ID != "this" ) { $CHECK_ID = "document.getElementById('".$CHECK_ID."')"; }
        if ( $ELEMENT_ID != "this" ) { $ELEMENT_ID = "document.getElementById('".$ELEMENT_ID."')"; }

        return "colorEntry ( ".$CHECK_ID.", '".$DEFAULTCOLOR."', '".$CHECKEDCOLOR."', ".$ELEMENT_ID." );";
}

function color_click_entry ( $CHECK_ID, $DEFAULTCOLOR, $CHECKEDCOLOR, $ELEMENT_ID, $RESET = FALSE, $RESETCOLOR = "transparent" )
{
        if ( $CHECK_ID != "this" ) { $CHECK_ID = "document.getElementById('".$CHECK_ID."')"; }
        if ( $ELEMENT_ID != "this" ) { $ELEMENT_ID = "document.getElementById('".$ELEMENT_ID."')"; }

        $js = "createClick ( ".$CHECK_ID.", '".$DEFAULTCOLOR."', '".$CHECKEDCOLOR."', ".$ELEMENT_ID." );";

        if ( $RESET == TRUE ) {
            $js .= " resetOld ( '".$RESETCOLOR."', last, lastBox, ".$ELEMENT_ID." );";
        }

        $js .= " saveLast ( ".$CHECK_ID.", ".$ELEMENT_ID." );";

        return $js;
}

function color_pre_selected ( $CHECK_ID, $ELEMENT_ID )
{
        $CHECK_ID = "document.getElementById('".$CHECK_ID."')";
        $ELEMENT_ID = "document.getElementById('".$ELEMENT_ID."')";

        return '
                <script type="text/javascript">
                        <!--
                    savePreSelectedLast ( '.$CHECK_ID.', '.$ELEMENT_ID.' );
                        //-->
                </script>
        ';
}

///////////////////////
//// Get Save Date ////
///////////////////////

function getsavedate ( $D, $M, $Y, $H = 0, $I = 0, $S = 0, $WITHOUT_MKTIME = FALSE )
{
        settype ( $D, "integer" );
        settype ( $M, "integer" );
        settype ( $Y, "integer" );
           settype ( $H, "integer" );
           settype ( $I, "integer" );
           settype ( $S, "integer" );

        $new_date = mktime ( $H, $I, $S, $M, $D, $Y );

        $savedate_arr['d'] = date ( "d", $new_date );
        $savedate_arr['m'] = date ( "m", $new_date );
        $savedate_arr['y'] = date ( "Y", $new_date );
        $savedate_arr['h'] = date ( "H", $new_date );
        $savedate_arr['i'] = date ( "i", $new_date );
        $savedate_arr['s'] = date ( "s", $new_date );

        if ( $WITHOUT_MKTIME == TRUE ) {
                $savedate_arr['d'] = $D;
                $savedate_arr['m'] = $M;
                $savedate_arr['y'] = $Y;
                $savedate_arr['h'] = $H;
                $savedate_arr['i'] = $I;
                $savedate_arr['s'] = $S;

                foreach ( $savedate_arr as $key => $value ) {
                        if ( $value == 0 ) {
                $savedate_arr[$key] = "";
                        } elseif ( $value < 10 ) {
                $savedate_arr[$key] = "0" . $value;
                        }
                }

                return $savedate_arr;
        }

        return $savedate_arr;
}

////////////////////////
//// Create JS-PoUp ////
////////////////////////

function openpopup ( $FILE, $WIDTH, $HEIGHT )
{
        $half_width = $WIDTH / 2;
        $half_height = $HEIGHT / 2;
        $javascript = 'open("'.$FILE.'","_blank","width='.$WIDTH.',height='.$HEIGHT.',top="+((screen.height/2)-'.$half_height.')+",left="+((screen.width/2)-'.$half_width.')+",scrollbars=YES,location=YES,status=YES")';

        return $javascript;
}

///////////////////////////////////
//// Create JS-FullScreen-PoUp ////
///////////////////////////////////

function open_fullscreenpopup ( $FILE )
{
        $javascript = 'open("'.$FILE.'","_blank","width="+screen.availWidth+",height="+screen.availHeight+",top=0,left=0,scrollbars=YES,location=YES,status=YES")';

        return $javascript;
}

/////////////////////////////
//// selected="selected" ////
/////////////////////////////

function getselected ( $VALUE, $COMPAREWITH )
{
        if ( $VALUE === $COMPAREWITH ) {
            return 'selected';
        } else {
                return '';
        }
}

///////////////////////////
//// checked="checked" ////
///////////////////////////

function getchecked ( $VALUE, $COMPAREWITH )
{
        if ( $VALUE === $COMPAREWITH ) {
            return 'checked';
        } else {
                return '';
        }
}

/////////////////////////////
//// disabled="disabled" ////
/////////////////////////////

function getdisabled ( $VALUE, $COMPAREWITH )
{
        if ( $VALUE === $COMPAREWITH ) {
            return 'disabled';
        } else {
                return '';
        }
}

/////////////////////////////
//// readonly="readonly" ////
/////////////////////////////

function getreadonly ( $VALUE, $COMPAREWITH )
{
        if ( $VALUE === $COMPAREWITH ) {
            return 'readonly';
        } else {
                return '';
        }
}

////////////////////////////////
//// Systemmeldung ausgeben ////
////////////////////////////////

function get_systext ( $MESSAGE, $TITLE = FALSE, $RED = FALSE, $IMAGE = FALSE )
{
    global $admin_phrases;

    if ( $TITLE == FALSE ) {
        $TITLE = $admin_phrases[common][system_message];
    }

    if ( $RED == TRUE ) {
        $class = "line_red";
    } else {
        $class = "line";
    }

    if ( $IMAGE == FALSE ) {
        return '
            <table class="configtable" cellpadding="4" cellspacing="0">
                <tr><td class="'.$class.'">'.$TITLE.'</td></tr>
                <tr><td class="config">'.$MESSAGE.'</td></tr>
                <tr><td class="space"></td></tr>
            </table>
        ';
    } else {
        return '
            <table class="configtable" cellpadding="4" cellspacing="0">
                <tr><td class="'.$class.'" colspan="2">'.$TITLE.'</td></tr>
                <tr>
                    <td class="config middle">'.$IMAGE.'</td>
                    <td class="config middle" width="100%">'.$MESSAGE.'</td>
                </tr>
                <tr><td class="space" colspan="2"></td></tr>
            </table>
        ';
    }
}

function systext ( $MESSAGE, $TITLE = FALSE, $RED = FALSE, $IMAGE = FALSE )
{
    echo get_systext ( $MESSAGE, $TITLE, $RED, $IMAGE );
}

/////////////////////////////////
//// JS Time-Button erzeugen ////
/////////////////////////////////

function js_timebutton ( $DATA, $CAPTION, $CLASS = "button" )
{
        $template = '<input class="'.$CLASS.'" type="button" value="'.$CAPTION.'" onClick="';

        foreach ( $DATA as $key => $value ) {
                $template .= "document.getElementById('".$key."').value='".$value."';";
        }

    $template .= '">';

        return $template;
}

////////////////////////////////
//// JS Now-Button erzeugen ////
////////////////////////////////

function js_nowbutton ( $DATA, $CAPTION, $CLASS = "button" )
{
        $template = '<input class="'.$CLASS.'" type="button" value="'.$CAPTION.'" onClick="';

        foreach ( $DATA as $key => $value ) {
                $id[] = $value;
        }
        unset ( $value );

        $value[] = "getCurDate()";
        $value[] = "getCurMonth()";
        $value[] = "getCurYear()";
        $value[] = "getCurHours()";
        $value[] = "getCurMinutes()";
        $value[] = "getCurSeconds()";

        for ( $i = 0; $i < count ( $id ) && $i < 6; $i++ ) {
                $template .= "document.getElementById('".$id[$i]."').value=".$value[$i].";";
        }

    $template .= '">';

        return $template;
}

////////////////////////////
//// Update from $_POST ////
////////////////////////////

function getfrompost ( $ARRAY )
{
        foreach ( $ARRAY as $key => $value  ) {
        $ARRAY[$key] = $_POST[$key];
        }
        return $ARRAY;
}

///////////////////////////////
//// Update $_POST from DB ////
///////////////////////////////

function putintopost ( $ARRAY )
{
        foreach ( $ARRAY as $key => $value  ) {
        $_POST[$key] = $ARRAY[$key];
        }
}

////////////////////////////////
//// Create textarea        ////
////////////////////////////////

function create_editor($name, $text="", $width="", $height="", $class="", $do_smilies=true)
{
    global $global_config_arr;
    global $db;

    if ($name != "") {
        $name2 = 'name="'.$name.'" id="'.$name.'"';
    } else {
        return false;
    }

    if ( $width != "" && is_int ( $width ) ) {
        $width2 = 'width:'.$width.'px;';
    } elseif ( $width != "" ) {
        $width2 = 'width:'.$width.';';
        }

    if ( $height != "" && is_int ( $height ) ) {
        $height2 = 'height:'.$height.'px;';
    } elseif ( $width != "" ) {
        $height2 = 'height:'.$height.';';
        }

    if ($class != "") {
        $class2 = 'class="nomonospace '.$class.'"';
    } else {
        $class2 = 'class="nomonospace"';
    }

    $style = $name2.' '.$class2.' style="'.$width2.' '.$height2.'"';

  $smilies = "";
  if ($do_smilies == true)
  {
    $smilies = '
    <td style="padding-left: 4px;">
      <fieldset style="width:46px;">
        <legend class="small" align="left"><font class="small">Smilies</font></legend>
          <table cellpadding="2" cellspacing="0" border="0" width="100%">';

    $zaehler = 0;
    $index = mysql_query("SELECT * FROM ".$global_config_arr['pref']."smilies ORDER by `order` ASC LIMIT 0, 10", $db);
    while ($smilie_arr = mysql_fetch_assoc($index))
    {
        $smilie_arr[url] = image_url("images/smilies/", $smilie_arr[id]);

        $smilie_template = '<td><img src="'.$smilie_arr[url].'" alt="'.$smilie_arr[replace_string].'" onClick="insert(\''.$name.'\', \''.$smilie_arr[replace_string].'\', \'\')" class="editor_smilies" /></td>';

        $zaehler += 1;
        switch ($zaehler)
        {
            case 1:
                $smilies .= "<tr align=\"center\">\n\r";
                $smilies .= $smilie_template;
                break;
             case 2:
                $zaehler = 0;
                $smilies .= $smilie_template;
                $smilies .= "</tr>\n\r";
                break;
        }
    }
    unset($smilie_arr);
    unset($smilie_template);
    unset($config_arr);

    $smilies .= '</table></fieldset></td>';
  }

    $buttons = "";
    $buttons .= create_editor_button_new('admin/editor/b.jpg', "B", "fett", "insert('$name', '[b]', '[/b]')");
    $buttons .= create_editor_button_new('admin/editor/i.jpg', "I", "kursiv", "insert('$name', '[i]', '[/i]')");
    $buttons .= create_editor_button_new('admin/editor/u.jpg', "U", "unterstrichen", "insert('$name','[u]','[/u]')");
    $buttons .= create_editor_button_new('admin/editor/s.jpg', "S", "durgestrichen", "insert('$name', '[s]', '[/s]')");
    $buttons .= create_editor_seperator();
    $buttons .= create_editor_button_new('admin/editor/center.jpg', "CENTER", "zentriert", "insert('$name', '[center]', '[/center]')");
    $buttons .= create_editor_seperator();
    $buttons .= create_editor_button_new('admin/editor/font.jpg', "FONT", "Schriftart", "insert_com('$name', 'font', 'Bitte gib die gew�nschte Schriftart ein:', '')");
    $buttons .= create_editor_button_new('admin/editor/color.jpg', "COLOR", "Schriftfarbe", "insert_com('$name', 'color', 'Bitte gib die gew�nschte Schriftfarbe (englisches Wort) ein:', '')");
    $buttons .= create_editor_button_new('admin/editor/size.jpg', "SIZE", "Schriftgr��e", "insert_com('$name', 'size', 'Bitte gib die gew�nschte Schriftgr��e (Zahl von 0-7) ein:', '')");
    $buttons .= create_editor_seperator();
    $buttons .= create_editor_button_new('admin/editor/img.jpg', "IMG", "Bild einf�gen", "insert_mcom('$name', '[img]', '[/img]', 'Bitte gib die URL zu der Grafik ein:', 'http://')");
    $buttons .= create_editor_button_new('admin/editor/cimg.jpg', "CIMG", "Content-Image einf�gen", "insert_mcom('$name', '[cimg]', '[/cimg]', 'Bitte gib den Namen des Content-Images (mit Endung) ein:', '')");
    $buttons .= create_editor_seperator();
    $buttons .= create_editor_button_new('admin/editor/url.jpg', "URL", "Link einf�gen", "insert_com('$name', 'url', 'Bitte gib die URL ein:', 'http://')");
    $buttons .= create_editor_button_new('admin/editor/home.jpg', "HOME", "Projektinternen Link einf�gen", "insert_com('$name', 'home', 'Bitte gib den projektinternen Verweisnamen ein:', '')");
    $buttons .= create_editor_button_new('admin/editor/email.jpg', "@", "Email-Link einf�gen", "insert_com('$name', 'email', 'Bitte gib die Email-Adresse ein:', '')");
    $buttons .= create_editor_seperator();
    $buttons .= create_editor_button_new('admin/editor/code.jpg', "C", "Code-Bereich einf�gen", "insert('$name', '[code]', '[/code]')");
    $buttons .= create_editor_button_new('admin/editor/quote.jpg', "Q", "Zitat einf�gen", "insert('$name', '[quote]', '[/quote]')");
    $buttons .= create_editor_button_new('admin/editor/noparse.jpg', "N", "Nicht umzuwandelnden Bereich einf�gen", "insert('$name', '[noparse]', '[/noparse]')");


    $textarea = '<table cellpadding="0" cellspacing="0" border="0" style="padding-bottom:4px">
                     <tr valign="bottom">
                         {buttons}
                     </tr>
                 </table>
                 <table cellpadding="0" cellspacing="0" border="0" width="100%">
                     <tr valign="top">
                         <td width="100%">
                             <textarea {style}>{text}</textarea>
                         </td>
                         {smilies}
                     </tr>
                 </table><br />';

    $textarea = str_replace("{style}", $style, $textarea);
    $textarea = str_replace("{text}", $text, $textarea);
    $textarea = str_replace("{buttons}", $buttons, $textarea);
    $textarea = str_replace("{smilies}", $smilies, $textarea);

    return $textarea;
}


////////////////////////////////
//// Create textarea Button ////
////////////////////////////////

function create_editor_button($img_url, $alt, $title, $insert)
{
    global $global_config_arr;
    $javascript = 'onClick="'.$insert.'"';

    $button = '
    <td class="editor_td">
        <div class="ed_button" {javascript}>
            <img src="{img_url}" alt="{alt}" title="{title}" />
        </div>
    </td>';
    $button = str_replace("{img_url}", $global_config_arr[virtualhost].$img_url, $button);
    $button = str_replace("{alt}", $alt, $button);
    $button = str_replace("{title}", $title, $button);
    $button = str_replace("{javascript}", $javascript, $button);

    return $button;
}

////////////////////////////////
//// Create textarea Button ////
////////////////////////////////

function create_editor_button_new($img_url, $alt, $title, $insert)
{
    global $global_config_arr;
    $javascript = 'javascript:'.$insert;

    $button = '
    <td class="editor_td">
        <a class="ed_button_new" style="background-image:url({img_url});"  href="{javascript}" title="{title}">
            <img border="0" src="img/null.gif" alt="{alt}">
        </a>
    </td>';
    $button = str_replace("{img_url}", $global_config_arr['virtualhost'].$img_url, $button);
    $button = str_replace("{alt}", $alt, $button);
    $button = str_replace("{title}", $title, $button);
    $button = str_replace("{javascript}", $javascript, $button);

    return $button;
}


////////////////////////////////////
//// Create textarea  Seperator ////
////////////////////////////////////

function create_editor_seperator()
{
    $seperator = '<td class="editor_td_seperator"></td>';
    return $seperator;
}


////////////////////////
//// Insert Tooltip ////
////////////////////////

function insert_tt ( $TITLE, $TEXT, $FORM_ID, $NEW_LINE = TRUE, $INSERT = TRUE, $BOLD_TITLE = TRUE, $SHOW_TITLE = TRUE   )
{
    if ( $NEW_LINE == TRUE ) {
        $span_start = '<span style="padding-bottom:3px; display:block;">';
        $span_end = '</span>';
    }
    if ( $INSERT == TRUE ) {
        $insert_button = '
        <a href="javascript:insert(\''.$FORM_ID.'\',\''.$TITLE.'\',\'\');"><img border="0" src="icons/pointer.gif" alt="->" title="einf�gen" align="absmiddle"></a>';
    }
    if ( $SHOW_TITLE == TRUE ) {
        $first_title = $TITLE."";
    }
    if ( $BOLD_TITLE == TRUE ) {
        $second_title = " <b>".$TITLE."</b>";
    } else {
        $second_title = "&nbsp;".$TITLE;
    }

    $template = $span_start.'
            '.$first_title.'
            <a class="tooltip" href="#?">
                <img border="0" src="icons/help.gif" align="absmiddle" alt="?">
                <span>
                    <img border="0" src="img/pointer.png" align="absmiddle" alt="->">'.$second_title.'<br>'.$TEXT.'
                </span>
            </a>'.$insert_button.$span_end.'
    ';

   return $template;
}

////////////////////////////////
//// Seitentitel generieren  ///
//// und Berechtigung pr�fen ///
////////////////////////////////

function createpage ( $TITLE, $PERMISSION, $FILE, $ACTIVE_MENU )
{
    global $TEXT;

    if ( $PERMISSION == 1 ) {
            $PAGE_DATA_ARR['title'] = $TITLE;
            $PAGE_DATA_ARR['file'] = $FILE;
            $PAGE_DATA_ARR['menu'] = $ACTIVE_MENU;
    } else {
            $PAGE_DATA_ARR['title'] = $TEXT['menu']->get("admin_error_page_title");
            $PAGE_DATA_ARR['file'] = "admin_error.php";
            $PAGE_DATA_ARR['menu'] = "none";
    }
    $PAGE_DATA_ARR['created'] = TRUE;
    return $PAGE_DATA_ARR;
}

////////////////////////////////
//// Men� erzeugen           ///
////////////////////////////////

function create_menu ( $ACTIVE_MENU )
{
    global $global_config_arr, $db, $TEXT;

    unset($MENU_ARR);

    $index = mysql_query ( "
                                    SELECT `page_id`, `page_file`
                                    FROM `".$global_config_arr['pref']."admin_cp`
                                    WHERE `group_id` = -1 AND `page_int_sub_perm` = 0
                                    ORDER BY `page_pos` ASC, `page_file` ASC
    ", $db );

    $template = "";
    while ( $MENU_ARR = mysql_fetch_assoc ( $index ) ) {
        $MENU_ARR['show'] = create_menu_show ( $MENU_ARR['page_file'] );

        if ( $MENU_ARR['show'] == true && $_SESSION['user_level'] == "authorised" )
        {
            if ( $ACTIVE_MENU == $MENU_ARR['page_file'] ) {
                $MENU_ARR['class'] = "menu_link_selected";
            } else {
                $MENU_ARR['class'] = "menu_link";
            }
            $template .= '<a href="?go='.$MENU_ARR['page_id'].'" target="_self" class="'.$MENU_ARR['class'].'">'.$TEXT['menu']->get("menu_".$MENU_ARR['page_file']).'</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;';
        }
    }

        $template = substr ( $template, 0, -24 );

    return $template;
}


////////////////////////////////
//// Men� anzeigen           ///
////////////////////////////////

function create_menu_show ( $MENU_ID ){
  global $sql;

  $index = $sql->query("
    SELECT A.`page_id`
    FROM `{..pref..}admin_cp` A, `{..pref..}admin_groups` B
    WHERE B.`menu_id`='$MENU_ID'
    AND B.`group_id`=A.`group_id`
    AND A.`page_int_sub_perm` = 0
    ORDER BY A.`page_id` ASC
  ");

  while(($page = mysql_fetch_assoc($index)) !== false) {
    $page = $page['page_id'];
    if ( isset($_SESSION[$page]) && $_SESSION[$page] == 1 ) {
      return TRUE;
      break;
    }
  }

  return FALSE;
}

/////////////////////////////////////////////////
//// Create Navigation Groups from Database  ////
/////////////////////////////////////////////////

function create_navi (  $ACTIVE_MENU, $GO  )
{
        global $global_config_arr, $db;

        unset ( $template );

        $groupaction = mysql_query ( "
                                            SELECT `group_id`
                                            FROM `".$global_config_arr['pref']."admin_groups`
                                            WHERE `menu_id` = '".$ACTIVE_MENU."'
                                            AND `group_id` != '0'
                                            AND `group_id` != '-1'
                                            ORDER BY `group_pos` ASC
        ", $db );

        $template = "";
        while ( $GROUP_ARR = mysql_fetch_assoc ( $groupaction ) ) {
            $template .= create_group ( $GROUP_ARR, create_group_first ( $template ), $GO );
        }

        return $template;
}
////////////////////////////////
//// Navi erzeugen           ///
////////////////////////////////

function create_group ($GROUP_ARR, $IS_FIRST, $GO )
{
    global $global_config_arr, $db, $TEXT;

    unset ( $template );

    // get links from database
    $pageaction = mysql_query ( "
                                    SELECT `page_id`
                                    FROM `".$global_config_arr['pref']."admin_cp`
                                    WHERE `group_id` = '".$GROUP_ARR['group_id']."' AND `page_int_sub_perm` = 0
                                    ORDER BY `page_pos` ASC, `page_id` ASC
    ", $db );
    $template = "";
    while ( $PAGE_ARR = mysql_fetch_assoc ( $pageaction ) ) {
        $template .= create_link ( $PAGE_ARR['page_id'], $GO );
    }

    // is group first in navi?
    if ( $IS_FIRST == TRUE ) {
        $headline_img = "navi_top";
    } else {
        $headline_img = "navi_headline";
    }

    // put links into group
    if ( strlen ( $template ) > 0 ) {
                $template = '
            <div class="'.$headline_img.'">
                <img src="img/pointer.png" alt="->" style="vertical-align:text-bottom">&nbsp;<b>'.$TEXT['menu']->get("group_".$GROUP_ARR['group_id']).'</b>
                <div class="navi_link">
                    '.$template.'
                </div>
            </div>';
    }

    return $template;
}

////////////////////////////////
//// Navi first              ///
////////////////////////////////

function create_group_first ( $TEMPLATE )
{
    if ( strlen ( $TEMPLATE ) == 0 ) {
        return TRUE;
    } else {
        return FALSE;
    }
}


////////////////////////////////
//// Seitenlink generieren   ///
//// und Berechtigung pr�fen ///
////////////////////////////////

function create_link ( $PAGE_ID, $GO )
{
    global $TEXT;

    // is active page?
    if ( $PAGE_ID == $GO ) {
        $link_class = "navi_selected";
    } else {
        $link_class = "navi";
    }

    // permission ok?
    if ( $_SESSION[$PAGE_ID] ) {
        return '<a href="?go='.$PAGE_ID.'" class="navi">- <span class="'.$link_class.'">'.$TEXT['menu']->get("page_link_".$PAGE_ID).'</span></a><br>';
    } else {
        return "";
    }
}



////////////////////////////////
//////// Cookie setzen /////////
////////////////////////////////

function admin_set_cookie($username, $password)
{
    global $global_config_arr;
    global $db;

    $username = savesql($username);
    $password = savesql($password);
    $index = mysql_query("select * from ".$global_config_arr['pref']."user where user_name = '$username'", $db);
    $rows = mysql_num_rows($index);
    if ($rows == 0)
    {
        return false;
    }
    else
    {
        $USER_ARR = mysql_fetch_assoc ( $index );
        if ( $USER_ARR['user_is_staff'] == 1 || $USER_ARR['user_is_admin'] == 1 || $USER_ARR['user_id'] == 1 ) {
            $dbisadmin = 1;
        } else {
            $dbisadmin = 0;
        }

        if ($dbisadmin == 1)
        {
            $dbuserpass = mysql_result($index, 0, "user_password");
            $dbuserid = mysql_result($index, 0, "user_id");
            $dbusersalt= mysql_result($index, 0, "user_salt");
            $password = md5 ( $password.$dbusersalt );

            if ($password == $dbuserpass)
            {
                $inhalt = $password . $username;
                setcookie ("login", $inhalt, time()+2592000, "/");
                return true;  // Login akzeptiert
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}

////////////////////////////////
/////// Logindaten pr�fen //////
////////////////////////////////

function admin_login($username, $password, $iscookie)
{
    global $global_config_arr;
    global $db;

    $username = savesql($username);
    $password = savesql($password);
    $index = mysql_query("SELECT * FROM ".$global_config_arr['pref']."user WHERE user_name = '$username'", $db);
    $rows = mysql_num_rows($index);
    if ($rows == 0)
    {
        return 1;  // Fehlercode 1: User nicht vorhanden
    }
    else
    {
        $USER_ARR = mysql_fetch_assoc ( $index );
        if ( $USER_ARR['user_is_staff'] == 1 || $USER_ARR['user_is_admin'] == 1 || $USER_ARR['user_id'] == 1 ) {
            $dbisadmin = 1;
        } else {
            $dbisadmin = 0;
        }

        if ($dbisadmin == 1)
        {
            $dbuserpass = mysql_result($index, 0, "user_password");
            $dbuserid = mysql_result($index, 0, "user_id");
            $dbusersalt = mysql_result($index, 0, "user_salt");

            if ($iscookie===false)
            {
                $password = md5 ( $password.$dbusersalt );
            }

            if ($password == $dbuserpass)
            {
                $_SESSION["user_level"] = "authorised";
                fillsession($dbuserid);
                return 0;  // Login akzeptiert
            }
            else
            {
                return 2;  // Fehlercode 2: Falsches Passwort
            }
        }
        else
        {
            return 3;  // Fehlercode 3: Keine Zugriffsrechte auf die Admin
        }
    }
}

////////////////////////////////
//////// Session f�llen ////////
////////////////////////////////

function fillsession($uid){
  global $sql;

  $USER_ARR = $sql->getData("user", "user_id, user_name, user_is_staff, user_group, user_is_admin", "WHERE `user_id` = '".$uid."' LIMIT 0,1", 1);

  $_SESSION['user_id']        =  $USER_ARR['user_id'];
  $_SESSION['user_name']      =  $USER_ARR['user_name'];
  $_SESSION['user_is_staff']  =  $USER_ARR['user_is_staff'];
  if($USER_ARR['user_id'] == 1)
    $USER_ARR['user_is_admin'] = 1;

  $permissions = $sql->getData("admin_cp", "page_id", "WHERE `group_id` != '-1' ORDER BY `page_id` ASC");

  foreach($permissions as $permission){
    $permission = $permission['page_id'];
    if ($USER_ARR['user_is_admin'] == 1) { // user ist admin or superadmin
      $_SESSION[$permission] = 1;
    } else {
      $group_granted = $sql->getData("user_permissions", "*", "WHERE `perm_id` = '".$permission."' AND `perm_for_group` = '1' AND `x_id` = '".$USER_ARR['user_group']."' AND `x_id` != '0' LIMIT 0,1", 2);

      $user_granted = $sql->getData("user_permissions", "*", "WHERE `perm_id` = '".$permission."' AND `perm_for_group` = '0' AND `x_id` = '".$USER_ARR['user_id']."' AND `x_id` != '0' LIMIT 0,1", 2);

      if ( $group_granted == 1 || $user_granted == 1 ) {
        $_SESSION[$permission] = 1;
      } else {
        $_SESSION[$permission] = 0;
      }
    }
  }

  // startpage permissions
  $permissions = $sql->getData("admin_cp", "page_id, page_file", "WHERE `group_id` = '-1' ORDER BY `page_id` ASC");

  foreach($permissions as $permission) {
    if($USER_ARR['user_is_admin'] == 1 ) {
      $_SESSION[$permission['page_id']] = 1;
    } else {
      if ( create_menu_show ( $permission['page_file'] ) == TRUE ) {
        $_SESSION[$permission['page_id']] = 1;
      } else {
        $_SESSION[$permission['page_id']] = 0;
      }
    }
  }
}

?>