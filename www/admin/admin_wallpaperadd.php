<?php if (!defined('ACP_GO')) die('Unauthorized access!');

/////////////////////
//// Config laden ///
/////////////////////
$FD->loadConfig('screens');
$config_arr = $FD->configObject('screens')->getConfigArray();

/////////////////////////////
//// Screenshot hochladen ///
/////////////////////////////

if (isset($_FILES['sizeimg_0']) AND isset($_POST['size']['0']) AND isset($_POST['wallpaper_name']) AND isset($_POST['wpadd']))
{
    $_POST['wallpaper_name'] = savesql($_POST['wallpaper_name']);
    $_POST['wallpaper_title'] = savesql($_POST['wallpaper_title']);

$index = mysql_query('SELECT * FROM '.$FD->config('pref')."wallpaper WHERE wallpaper_name = '$_POST[wallpaper_name]'", $FD->sql()->conn());
if (mysql_num_rows($index)==0) {

    for ($i=1; $i<=$_POST['options']; $i++)
    {
      $j = $i - 1;
      $_POST['size'][$j] = savesql($_POST['size'][$j]);
      $filesname = "sizeimg_$j";
      if (!isset($_FILES[$filesname]))
        $_POST['size'][$j] = '';
    }

    $_POST['catid'] = intval($_POST['catid']);
    mysql_query('INSERT INTO '.$FD->config('pref')."wallpaper (wallpaper_name, wallpaper_title, cat_id)
                 VALUES ('".$_POST['wallpaper_name']."',
                         '".$_POST['wallpaper_title']."',
                         '".$_POST['catid']."')", $FD->sql()->conn());
    $wp_id = mysql_insert_id();

    $message = '';

    for ($i=1; $i<=$_POST['options']; $i++)
    {
      $j = $i - 1;
      $filesname = "sizeimg_$j";
      if (isset($_FILES[$filesname]) AND $_POST['size'][$j] != '')
      {
        $j = $i - 1;
        $upload = upload_img($_FILES[$filesname], 'images/wallpaper/', $_POST['wallpaper_name'].'_'.$_POST['size'][$j], $config_arr['wp_size']*1024, $config_arr['wp_x'], $config_arr['wp_y']);
        $message .= "WP Gr&ouml;&szlig;e $i: ".upload_img_notice($upload).'<br>';
        switch ($upload)
        {
        case 0:
          mysql_query('INSERT INTO '.$FD->config('pref')."wallpaper_sizes (wallpaper_id, size)
                       VALUES ('".$wp_id."',
                               '".$_POST['size'][$j]."')", $FD->sql()->conn());
          break;
        }

      }
    }
    if (image_exists('images/wallpaper/', $_POST['wallpaper_name'].'_'.$_POST['size'][0]))
    {
      create_thumb_from(image_url('images/wallpaper/', $_POST['wallpaper_name'].'_'.$_POST['size'][0], FALSE, TRUE), $config_arr['wp_thumb_x'], $config_arr['wp_thumb_y']);
      $message .= create_thumb_notice($upload).'<br>';
      image_rename('images/wallpaper/', $_POST['wallpaper_name'].'_'.$_POST['size'][0].'_s', $_POST['wallpaper_name'].'_s');
    }

  $message .= '<br>Weiteres Wallpaper hinzuf&uuml;gen:';
  systext($message);
  $_POST['wallpaper_name'] = '';
  $_POST['wallpaper_title'] = '';
  for ($i=1; $i<=$_POST['options']; $i++)
  {
    $j = $i - 1;
    $_POST['size'][$j] = '';
  }
}
else
  {
    systext('Es existiert bereits ein Wallpaper mit diesem Namen!');
  }

}

////////////////////////////
//// Wallpaper Formular ////
////////////////////////////

    if (!isset($_POST['options']))
    {
        $_POST['options'] = 5;
    }
    if (!isset($_POST['optionsadd']))
    {
        $_POST['optionsadd'] = 0;
    }
    $_POST['options'] = $_POST['options'] + $_POST['optionsadd'];

    if (!isset($_POST['wallpaper_name'])) $_POST['wallpaper_name'] = '';
    if (!isset($_POST['wallpaper_title'])) $_POST['wallpaper_title'] = '';

echo'
                    <form id="form" action="" enctype="multipart/form-data" method="post">
                        <input id="send" type="hidden" value="0" name="wpadd">
                        <input type="hidden" value="'.$_POST['options'].'" name="options">
                        <input type="hidden" value="wp_add" name="go">
                        <table border="0" cellpadding="4" cellspacing="0" width="600">
                            <tr>
                                <td class="config" valign="top" width="150">
                                    Dateiname:<br>
                                    <font class="small">Name unter dem gespeichert wird.</font>
                                </td>
                                <td class="config" valign="top" width="450">
                                    <input class="text" name="wallpaper_name" size="33" maxlength="100" value="'.$_POST['wallpaper_name'].'">
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Titel:<br>
                                    <font class="small">Titel des Wallpapers.<br>(optional)</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" name="wallpaper_title" size="33" maxlength="255" value="'.$_POST['wallpaper_title'].'">
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
$index = mysql_query('SELECT * FROM '.$FD->config('pref').'screen_cat WHERE cat_type = 2', $FD->sql()->conn());
while ($cat_arr = mysql_fetch_assoc($index))
{
    echo'
                                        <option value="'.$cat_arr['cat_id'].'">
                                            '.$cat_arr['cat_name'].'
                                        </option>
    ';
}
echo'
                                    </select>
                                </td>
                            </tr>';

    for ($i=1; $i<=$_POST['options']; $i++)
    {
        $j = $i - 1;
        if (isset($_POST['size'][$j]))
        {
            echo'
                            <tr>
                                <td class="config" valign="top">
                                    Gr&ouml;&szlig;e '.$i.':<br>
                                    <font class="small">Format und WP ausw&auml;hlen.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" id="size'.$j.'" name="size['.$j.']" size="12" maxlength="30" value="'.$_POST['size'][$j].'">&nbsp;
                                    <select onChange=\'document.getElementById("size'.$j.'").value=this.value; this.selectedIndex = 0\'>
                                        <option value="">Gr&ouml;&szlig;e ausw&auml;hlen...</option>
                                        <option value="">-----------</option>
                                        <option value="800x600">800x600</option>
                                        <option value="1024x768">1024x768</option>
                                        <option value="1280x768">1280x768</option>
                                        <option value="1280x800">1280x800</option>
                                        <option value="1280x1024">1280x1024</option>
                                        <option value="1366x768">1366x768</option>
                                        <option value="1440x900">1440x900</option>
                                        <option value="1680x1050">1680x1050</option>
                                        <option value="1920x1080">1920x1080</option>
                                        <option value="1920x1200">1920x1200</option>
                                    </select><br>
                                    <input type="file" class="text" name="sizeimg_'.$j.'" size="40">
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
                                    Gr&ouml;&szlig;e '.$i.':<br>
                                    <font class="small">Format und WP ausw&auml;hlen.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text left" id="size'.$j.'" name="size['.$j.']" size="12" maxlength="30" value="">&nbsp;
                                    <select onChange=\'document.getElementById("size'.$j.'").value=this.value; this.selectedIndex = 0\'>
                                        <option value="">Gr&ouml;&szlig;e ausw&auml;hlen...</option>
                                        <option value="">-----------</option>
                                        <option value="800x600">800x600</option>
                                        <option value="1024x768">1024x768</option>
                                        <option value="1280x768">1280x768</option>
                                        <option value="1280x800">1280x800</option>
                                        <option value="1280x1024">1280x1024</option>
                                        <option value="1366x768">1366x768</option>
                                        <option value="1440x900">1440x900</option>
                                        <option value="1680x1050">1680x1050</option>
                                        <option value="1920x1080">1920x1080</option>
                                        <option value="1920x1200">1920x1200</option>
                                    </select><br>
                                    <input type="file" class="text" name="sizeimg_'.$j.'" size="40">
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
                                    <input class="button" type="submit" value="Hinzuf&uuml;gen">
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
?>
