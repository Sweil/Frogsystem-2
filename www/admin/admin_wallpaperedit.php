<?php

//////////////////////
//// Wallpaper l�schen ////
//////////////////////
if ($_POST['wallpaper_id'] AND $_POST['sended'] == "edit" AND $_POST[size][0] AND $_POST['wpedit'])
{
    $_POST[wallpaper_name] = savesql($_POST[wallpaper_name]);
    $_POST[wallpaper_title] = savesql($_POST[wallpaper_title]);

    $index = mysql_query("SELECT * FROM fs_wallpaper WHERE wallpaper_name = '$_POST[wallpaper_name]'", $db);

    if (count($_POST[size]) == count(array_unique($_POST[size])) AND (mysql_num_rows($index)==0 OR $_POST[wallpaper_name] == $_POST[oldname]))
    {
    //IF Beginn
    
    $update = "UPDATE fs_wallpaper
               SET wallpaper_name = '$_POST[wallpaper_name]',
                   wallpaper_title  = '$_POST[wallpaper_title]',
                   cat_id      = '$_POST[catid]'
               WHERE wallpaper_id = $_POST[wallpaper_id]";
    mysql_query($update, $db);

    // Files  aktualisieren
        for ($i=0; $i<count($_POST[size]); $i++)
        {
            if ($_POST[delwp][$i])
            {
                settype($_POST[delwp][$i], 'integer');
                $index = mysql_query("SELECT size FROM fs_wallpaper_sizes WHERE size_id = '".$_POST[delwp][$i]."'", $db);
                $size_name = mysql_result($index, "size");
                mysql_query("DELETE FROM fs_wallpaper_sizes WHERE size_id = '".$_POST[delwp][$i]."'", $db);
                image_delete("../images/wallpaper/", "$_POST[oldname]_$size_name");
            }
            else
            {
                $filesname = "sizeimg_$i";
                if (isset($_FILES[$filesname]) && $_POST[wpnew][$i]==1 && $_POST[size][$i]!="")
                {
                    $upload = upload_img($_FILES[$filesname], "../images/wallpaper/", $_POST['oldname']."_".$_POST['size'][$i]."a", 5*1024*1024, 9999, 9999, 0, 0, false);
                    systext(upload_img_notice($upload));
                    switch ($upload)
                    {
                    case 0:
                        $insert = "INSERT INTO fs_wallpaper_sizes (wallpaper_id, size)
                                   VALUES ('".$_POST[wallpaper_id]."',
                                       '".$_POST[size][$i]."')";
                        mysql_query($insert, $db);
                    break;
                    }
                }
                elseif ($_POST[wpnew][$i]==0)
                {
                    $index = mysql_query("SELECT size FROM fs_wallpaper_sizes WHERE size_id = '".$_POST[size_id][$i]."'", $db);
                    $size_name = mysql_result($index, "size");
                    $rename_info = pathinfo(image_url("../images/wallpaper/", "$_POST[oldname]_$size_name", false));
                    rename(image_url("../images/wallpaper/", "$_POST[oldname]_$size_name", false), "../images/wallpaper/".$_POST[oldname]."_".$_POST[size][$i]."a.".$rename_info[extension]);
                    $update = "UPDATE fs_wallpaper_sizes
                               SET size = '".$_POST[size][$i]."'
                               WHERE size_id = ".$_POST[size_id][$i];
                    mysql_query($update, $db);
                    
                    if (isset($_FILES[$filesname]))
                    {
                        $upload = upload_img($_FILES[$filesname], "../images/wallpaper/", $_POST['oldname']."_".$_POST['size'][$i]."a", 5*1024*1024, 9999, 9999, 0, 0, false);
                        systext(upload_img_notice($upload));
                    }
                }
            }
        }

     //Rename
     $index2 = mysql_query("SELECT * FROM fs_wallpaper_sizes WHERE wallpaper_id = '$_POST[wallpaper_id]'", $db);
     while ($sizes_arr = mysql_fetch_assoc($index2))
     {
          $rename_info = pathinfo(image_url("../images/wallpaper/", "$_POST[oldname]_$sizes_arr[size]a", false));
          rename(image_url("../images/wallpaper/", "$_POST[oldname]_$sizes_arr[size]a", false), "../images/wallpaper/".$_POST[wallpaper_name]."_".$sizes_arr[size].".".$rename_info[extension]);
     }
     $rename_info = pathinfo(image_url("../images/wallpaper/", "$_POST[oldname]_s", false));
     rename(image_url("../images/wallpaper/", "$_POST[oldname]_s", false), "../images/wallpaper/".$_POST[wallpaper_name]."_s.".$rename_info[extension]);

   //IF Ende
   }
   else
   {
       systext('Fehler bei der Bearbeitung:<br><br>
       - Jede Gr��e darf nur einmal vorkommen<br>
       - Der Wallpapername muss einzigartig sein<br><br>
       Da eine oder beide Bedingungen nicht erf�llt ist/sind, wurde die Bearbeitung abgebrochen!');
   }
}
elseif ($_POST['wallpaper_id'] AND $_POST['sended'] == "delete")
{

    $index = mysql_query("SELECT * FROM fs_wallpaper WHERE wallpaper_id = '$_POST[wallpaper_id]'", $db);
    $wp_del_array = mysql_fetch_assoc($index);
    mysql_query("DELETE FROM fs_wallpaper WHERE wallpaper_id = '$_POST[wallpaper_id]'", $db);
    image_delete("../images/wallpaper/", $wp_del_array[wallpaper_name]."_s");
    
    $index = mysql_query("SELECT * FROM fs_wallpaper_sizes WHERE wallpaper_id = '$_POST[wallpaper_id]'", $db);
    while ($wp_sizes_del_array = mysql_fetch_assoc($index))
    {
      image_delete("../images/wallpaper/", $wp_del_array[wallpaper_name]."_".$wp_sizes_del_array[size]);
    }
    mysql_query("DELETE FROM fs_wallpaper_sizes WHERE wallpaper_id = '$_POST[wallpaper_id]'", $db);
          
    systext('Wallpaper wurden gel�scht');
}

//////////////////////////
/// Wallpaper Funktion ///
//////////////////////////

elseif ($_POST['wallpaper_id'] AND $_POST['wp_action'])
{

////////////////////////////
/// Wallpaper bearbeiten ///
////////////////////////////


  if ($_POST['wp_action'] == "edit")
  {
    $index = mysql_query("select * from fs_wallpaper WHERE wallpaper_id = '$_POST[wallpaper_id]'", $db);
    $admin_wp_arr = mysql_fetch_assoc($index);

    $admin_wp_arr['old_name'] = killhtml($admin_wp_arr['wallpaper_name']);

    $error_message = "";

    if (isset($_POST['sended']))
    {
      $admin_wp_arr['wallpaper_name'] = $_POST['wallpaper_name'];
      $admin_wp_arr['wallpaper_title'] = $_POST['wallpaper_title'];
      $admin_wp_arr['cat_id'] = $_POST['catid'];
      
      $error_message = "Bitte f�llen Sie <b>alle Pflichfelder</b> aus!";
    }
    systext($error_message);
    
    //EDIT ANFANG

    $index2 = mysql_query("select * from fs_wallpaper_sizes WHERE wallpaper_id = '$_POST[wallpaper_id]' ORDER BY size_id ASC", $db);
    $admin_sizes_arr = mysql_fetch_assoc($index2);

    for($i=0; $i<mysql_num_rows($index2); $i++)
    {
        $admin_sizes_arr[wp_exists][$i] = "Dieses Wallpaper existiert bereits! W�hlen sie nur ein Neues aus, wenn das Alte �berschrieben werden soll!<br>";
        if (!isset($_POST[size][$i]))
        {
            $_POST[size][$i] = mysql_result($index2, $i, "size");
        }
        if (!isset($_POST[size_id][$i]))
        {
            $_POST[size_id][$i] = mysql_result($index2, $i, "size_id");
        }
    }

    if (!isset($_POST[options]))
    {
        $_POST[options] = mysql_num_rows($index2);
    }
    $_POST[options] = $_POST[options] + $_POST[optionsadd];

    echo'
                    <form id="form" action="'.$PHP_SELF.'" enctype="multipart/form-data" method="post">
                        <input id="send" type="hidden" value="0" name="wpedit">
                        <input type="hidden" value="'.$_POST[options].'" name="options">
                        <input type="hidden" value="wallpaperedit" name="go">
                        <input type="hidden" value="'.session_id().'" name="PHPSESSID">
                        <input type="hidden" name="sended" value="edit" />
                        <input type="hidden" name="wp_action" value="'.$_POST[wp_action].'" />
                        <input type="hidden" name="wallpaper_id" value="'.$admin_wp_arr[wallpaper_id].'" />
                        <input type="hidden" name="oldname" value="'.$admin_wp_arr[old_name].'" />
                        <table border="0" cellpadding="4" cellspacing="0" width="600">
                            <tr>
                                <td class="config" valign="top" width="125px">
                                    Dateiname:<br>
                                    <font class="small">Name unter dem gespeichert wird.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" name="wallpaper_name" size="33" maxlength="100" value="'.$admin_wp_arr[wallpaper_name].'">
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Titel:<br>
                                    <font class="small">Titel des Wallpapers.<br>(optional)</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" name="wallpaper_title" size="33" maxlength="100" value="'.$admin_wp_arr[wallpaper_title].'">
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Kategorie:<br>
                                    <font class="small">Kategorie in die das WP eingeordnet wird</font>
                                </td>
                                <td class="config" valign="top">
                                    <select name="catid">
';
$index = mysql_query("SELECT * FROM fs_screen_cat WHERE cat_type = 2", $db);
while ($cat_arr = mysql_fetch_assoc($index))
{
    echo'
                                        <option value="'.$cat_arr[cat_id].'"';
                                        if ($cat_arr[cat_id] == $admin_wp_arr[cat_id])
                                            echo ' selected="selected"';
                                        echo '>'.$cat_arr[cat_name].'</option>
    ';
}
echo'
                                    </select>
                                </td>
                            </tr>';

    for ($i=1; $i<=$_POST[options]; $i++)
    {
        $j = $i - 1;
        if ($_POST[size][$j])
        {
            echo'
                            <tr>
                                <td class="config" valign="top">
                                    Gr��e '.$i.':<br>
                                    <font class="small">Format und WP ausw�hlen.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input type="hidden" name="wpnew['.$j.']" value="'.$_POST[wpnew][$j].'">
                                    <input type="hidden" name="size_id['.$j.']" value="'.$_POST[size_id][$j].'" />
                                    <input class="text" id="size'.$j.'" name="size['.$j.']" size="10" maxlength="30" value="'.$_POST[size][$j].'">
                                    <input type="file" class="text" name="sizeimg_'.$j.'" size="25">
                                    L�schen: <input name="delwp['.$j.']" value="'.$_POST[size_id][$j].'" type="checkbox"><br>
                                    '.$admin_sizes_arr['wp_exists'][$j].'
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="800x600";\' value="800x600">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1024x768";\' value="1024x768">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1280x960";\' value="1280x960">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1280x1024";\' value="1280x1024">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1600x1200";\' value="1600x1200">
                                    <br><br>
                                </td>
                            </tr>
            ';
        }
        else
        {
            echo'
                            <tr>
                                <td class="config" valign="top">
                                    Gr��e '.$i.':<br>
                                    <font class="small">Format und WP ausw�hlen.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input type="hidden" name="wpnew['.$j.']" value="1">
                                    <input class="text" id="size'.$j.'" name="size['.$j.']" size="10" maxlength="30" value=""> <input type="file" class="text" name="sizeimg_'.$j.'" size="33"><br>
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="800x600";\' value="800x600">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1024x768";\' value="1024x768">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1280x960";\' value="1280x960">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1280x1024";\' value="1280x1024">
                                    <input class="button" type="button" onClick=\'document.getElementById("size'.$j.'").value="1600x1200";\' value="1600x1200">
                                    <br><br>
                                </td>
                            </tr>
            ';
        }
    }

echo'
                            <tr>
                                <td class="configthin">
                                    &nbsp;
                                </td>
                                <td class="configthin">
                                    <input size="2" class="text" name="optionsadd">
                                    Wallpaper
                                    <input class="button" type="submit" value="Hinzuf�gen">
                                </td>
                            </tr>
                            <tr>
                                <td class="configthin">
                                    &nbsp;
                                </td>
                                <td align="left"><br>
                                    <input class="button" type="button" onClick="javascript:document.getElementById(\'send\').value=\'1\'; document.getElementById(\'form\').submit();" value="Absenden">
                                </td>
                            </tr>
                        </table>
                    </form>
    ';
  }

/////////////////////////
/// Wallpaper l�schen ///
/////////////////////////


  elseif ($_POST['wp_action'] == "delete")
  {
    $index = mysql_query("select * from fs_wallpaper WHERE wallpaper_id = '$_POST[wallpaper_id]'", $db);
    $wallpaper_arr = mysql_fetch_assoc($index);

echo '
<form action="'.$PHP_SELF.'" method="post">
<table width="100%" cellpadding="4" cellspacing="0">
<input type="hidden" value="wallpaperedit" name="go">
<input type="hidden" value="'.session_id().'" name="PHPSESSID">
<input type="hidden" name="sended" value="delete" />
<input type="hidden" name="wallpaper_id" value="'.$wallpaper_arr[wallpaper_id].'" />
       <tr align="left" valign="top">
           <td class="config" colspan="4">
               <b>Wallpaper l�schen:</b><br><br>
           </td>
       </tr>
       <tr align="left" valign="top">
           <td class="config" colspan="3">
               Soll das untenstehende Wallpaper wirklich gel�scht werden?
           </td>
           <td width="25%">
             <input type="submit" value="Ja" class="button" />  <input type="button" onclick="history.back(1);" value="Nein" class="button" />
           </td>
       <tr>
       <tr>
           <td class="config">
               <img src="'.image_url("../images/wallpaper/", $wallpaper_arr[wallpaper_name]."_s", false).'" width="100" height="75" alt=""><br>
           </td>
           <td class="configthin"><b>'.$wallpaper_arr[wallpaper_name].'</b>';

           $index2 = mysql_query("SELECT * FROM fs_wallpaper_sizes WHERE wallpaper_id = '$wallpaper_arr[wallpaper_id]' ORDER BY size_id ASC", $db);
           while ($sizes_arr = mysql_fetch_assoc($index2))
           {
             echo "<br>".$sizes_arr[size];
           }
           echo'</td>';
           $index2 = mysql_query("select cat_name from fs_screen_cat where cat_id = $wallpaper_arr[cat_id]", $db);
           $db_cat_name = mysql_result($index2, 0, "cat_name");
           echo'
           <td class="configthin">
               '.$db_cat_name.'
           </td>
       </tr>
</table></form>';
  }
}

///////////////////////////
/// Kategorie ausw�hlen ///
///////////////////////////

else
{
    if (isset($_POST[wpcatid]))
    {
        settype($_POST[wpcatid], 'integer');
        $wherecat = "WHERE cat_id = " . $_POST[wpcatid];
    }

    echo'
                    <form action="'.$PHP_SELF.'" method="post">
                        <input type="hidden" value="wallpaperedit" name="go">
                        <input type="hidden" value="'.session_id().'" name="PHPSESSID">
                        <table border="0" cellpadding="2" cellspacing="0" width="600">
                            <tr>
                                <td class="config" width="40%">
                                    Dateien der Kategorie
                                    <select name="wpcatid">
    ';
    $index = mysql_query("SELECT * FROM fs_screen_cat WHERE cat_type = 2", $db);
    while ($cat_arr = mysql_fetch_assoc($index))
    {
        $sele = ($_POST[wpcatid] == $cat_arr[cat_id]) ? "selected" : "";
        echo'
                                        <option value="'.$cat_arr[cat_id].'" '.$sele.'>
                                            '.$cat_arr[cat_name].'
                                        </option>
        ';
    }
    echo'
                                    </select>
                                    <input class="button" type="submit" value="Anzeigen">
                                </td>
                            </tr>
                        </table>
                    </form><br>
    ';


///////////////////////////
/// Wallpaper ausw�hlen ///
///////////////////////////

    if (isset($_POST[wpcatid]))
    {
        echo'
                    <form action="'.$PHP_SELF.'" method="post">
                        <input type="hidden" value="wallpaperedit" name="go">
                        <input type="hidden" value="'.session_id().'" name="PHPSESSID">
                        <table border="0" cellpadding="2" cellspacing="0" width="600">
                            <tr>
                                <td class="config" width="25%">
                                    Wallpaper
                                </td>
                                <td class="config" width="30%">
                                    Name / Gr��en
                                </td>
                                <td class="config" width="20%">
                                    Kategorie
                                </td>
                                <td class="config" width="25%">
                                </td>
                            </tr>
        ';
        $index = mysql_query("SELECT * FROM fs_wallpaper $wherecat ORDER BY wallpaper_id DESC", $db);
        while ($wallpaper_arr = mysql_fetch_assoc($index))
        {
            echo'
                    <form action="'.$PHP_SELF.'" method="post">
                        <input type="hidden" name="wallpaper_id" value="'.$wallpaper_arr[wallpaper_id].'" />
                        <input type="hidden" value="wallpaperedit" name="go">
                        <input type="hidden" value="'.session_id().'" name="PHPSESSID">
                            <tr>
                                <td class="config">
                                    <img src="'.image_url("../images/wallpaper/", $wallpaper_arr[wallpaper_name]."_s", false).'" width="100" height="75" alt=""><br>

                                </td>
                                <td class="configthin"><b>'.$wallpaper_arr[wallpaper_name].'</b>';

            $index2 = mysql_query("SELECT * FROM fs_wallpaper_sizes WHERE wallpaper_id = '$wallpaper_arr[wallpaper_id]' ORDER BY size_id ASC", $db);
            while ($sizes_arr = mysql_fetch_assoc($index2))
            {
              echo "<br>".$sizes_arr[size];
            }
            echo'</td>';
            $index2 = mysql_query("select cat_name from fs_screen_cat where cat_id = $wallpaper_arr[cat_id]", $db);
            $db_cat_name = mysql_result($index2, 0, "cat_name");
            echo'
                                <td class="configthin">
                                    '.$db_cat_name.'
                                </td>
                                <td class="configthin">
                                    <select name="wp_action" size="1" class="text">
                                        <option value="edit">Bearbeiten</option>
                                        <option value="delete">L�schen</option>
                                    </select> <input class="button" type="submit" value="Los" />
                                </td>
                            </tr>
                    </form>
            ';
        }
        echo'</table>';
    }
}
?>