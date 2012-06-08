<?php if (!defined('ACP_GO')) die('Unauthorized access!');

//////////////////////////
//// Referrer L�schen ////
//////////////////////////

if ( $_POST['delete_referrer'] == 1 && isset ( $_POST['del_days'] ) && isset ( $_POST['del_hits'] ) )
{
    settype ( $_POST['del_days'], 'integer' );
    settype ( $_POST['del_hits'], 'integer' );
    savesql ( $_POST['del_contact'] );
    savesql ( $_POST['del_age'] );
    savesql ( $_POST['del_amount'] );

	if ( $_POST['del_days'] < 1 )
    {
        systext ( $FD->text('page', 'referrer_not_enough_days'), $FD->text('page', 'error'), TRUE );
    }
	elseif ( $_POST['del_hits'] < 1 )
    {
        systext ( $FD->text('page', 'referrer_not_enough_hits'), $FD->text('page', 'error'), TRUE );
    }
    else
    {
        $del_date = time() - $_POST['del_days'] * 86400;

    	switch ( $_POST['del_contact'] )
    	{
        	case 'first': $contact = 'first'; break;
         	case 'last': $contact = 'last'; break;
    	}
    	switch ( $_POST['del_age'] )
    	{
        	case 'older': $age = '<'; break;
         	case 'younger': $age = '>'; break;
    	}
    	switch ( $_POST['del_amount'] )
    	{
        	case 'less': $amount = '<'; break;
         	case 'more': $amount = '>'; break;
    	}

		mysql_query('DELETE FROM '.$global_config_arr['pref'].'counter_ref
                     WHERE ref_'.$contact.' '.$age." '".$del_date."' AND
                           ref_count ".$amount." '".$_POST['del_hits']."'", $FD->sql()->conn() );

		$message =  $FD->text('page', 'referrer_deleted_entries') . ':<br>' .
					'"'.$admin_phrases['stats']['referrer_'.$_POST['del_contact']] . ' ' .
					$admin_phrases['stats']['referrer_delete_'.$_POST['del_age']] . ' ' .
        			$_POST['del_days'] . ' ' .
        			$FD->text('page', 'referrer_delete_days') . ' ' .
        			$FD->text('page', 'referrer_delete_and') . ' ' .
        			$admin_phrases['stats']['referrer_delete_'.$_POST['del_amount']] . ' ' .
        			$_POST['del_hits'] . ' ' .
        			$FD->text('page', 'referrer_delete_hits').'"' . '<br><br>' .
					$FD->text('page', 'affected_rows') . ': ' .
					mysql_affected_rows();

        systext ( $message, $FD->text('page', 'info') );
    }
}

//////////////////////////
/// Filter definieren ////
//////////////////////////

else
{
    if ( !isset ( $_POST['limit'] ) )
    {
        $_POST['limit'] = 50;
    }

    settype ( $_POST['limit'], 'integer' );
    $filter = savesql ( $_POST['filter'] );
    $_POST['filter'] = killhtml ( $_POST['filter'] );

    switch ( $_POST['order'] )
    {
        case 'u': $usel = 'selected'; break;
        case 'c': $csel = 'selected'; break;
        case 'l': $lsel = 'selected'; break;
        default:
			$fsel = 'selected';
			$_POST['order'] = 'f';
			break;
    }
    switch ( $_POST['sort'] )
    {
        case 'ASC': $ascsel = 'selected'; break;
        default:
			$descsel = 'selected';
			$_POST['sort'] = 'DESC';
			break;
    }

    echo'
					<form action="" method="post">
                        <input type="hidden" value="stat_ref" name="go">
                        <table class="configtable" cellpadding="4" cellspacing="0">
							<tr><td class="line" colspan="2">'.$FD->text('page', 'referrer_filter_title').'</td></tr>
							<tr>
                                <td class="config middle">
                                    '.$FD->text('page', 'referrer_show').':
                                </td>
                                <td class="config" width="100%">
                                    <input name="limit" size="4" maxlength="3" class="text" value="'.$_POST['limit'].'">
                                    '.$FD->text('page', 'referrer_orderby').'
                                    <select name="order">
                                        <option value="c" '.$csel.'>'.$FD->text('page', 'referrer_hits').'</option>
                                        <option value="f" '.$fsel.'>'.$FD->text('page', 'referrer_first').'</option>
                                        <option value="l" '.$lsel.'>'.$FD->text('page', 'referrer_last').'</option>
                                        <option value="u" '.$usel.'>'.$FD->text('page', 'referrer_url').'</option>
                                    </select>,
                                    <select name="sort">
                                        <option value="ASC" '.$ascsel.'>'.$FD->text('page', 'ascending').'</option>
                                        <option value="DESC" '.$descsel.'>'.$FD->text('page', 'descending').'</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="config middle">
                                    '.$FD->text('page', 'referrer_filter').':
                                </td>
                                <td class="config" width="100%">
                                    <input class="text" style="width: 100%;" name="filter" maxlength="255" value="'.$_POST['filter'].'">
                                </td>
                            </tr>
						</table>
                        <table class="configtable" cellpadding="4" cellspacing="0">
                            <tr>
                                <td class="config">
                                    <span class="small">
										'.$FD->text('page', 'referrer_filter_info1').'<br>
                                    	'.$FD->text('page', 'referrer_filter_info2').'
									</span>
                                </td>
                                <td align="right" valign="bottom">
                                    <input type="submit" value="'.$FD->text('page', 'show_button').'" class="button">
                                </td>
                            </tr>
                        </table>
					</form>
	';

//////////////////////////
/// Referrer anzeigen ////
//////////////////////////

    echo'
						<table class="configtable" cellpadding="4" cellspacing="0">
							<tr><td class="line">'.$FD->text('page', 'referrer_list_title').'</td></tr>
						</table>

						<table class="configtable" style="border-collapse: collapse; border: 1px solid #000000;" cellpadding="1" cellspacing="0" border="1">
						<tr>
                            <td class="h" align="center" colspan="5">
                                <b>'.$FD->text('page', 'referrer_table_title').'</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="h" align="center">
                                <b>'.$FD->text('page', 'referrer_table_url').'</b>
                            </td>
                            <td class="h" align="center">
                                <b>'.$FD->text('page', 'referrer_table_hits').'</b>
                            </td>
                            <td class="h" align="center">
                                <b>'.$FD->text('page', 'referrer_table_first').'</b>
                            </td>
                            <td class="h" align="center">
                                <b>'.$FD->text('page', 'referrer_table_last').'</b>
                            </td>
                        </tr>
	';

    $query = get_filter_where ( $filter, 'ref_url' );

    switch ( $_POST['order'] )
    {
        case 'u':
            $query .= ' ORDER BY ref_url '.$_POST['sort'].' LIMIT '.$_POST['limit'];
            break;
        case 'c':
            $query .= ' ORDER BY ref_count '.$_POST['sort'].' LIMIT '.$_POST['limit'];
            break;
        case 'l':
            $query .= ' ORDER BY ref_last '.$_POST['sort'].' LIMIT '.$_POST['limit'];
            break;
        default:
            $query .= ' ORDER BY ref_first '.$_POST['sort'].' LIMIT '.$_POST['limit'];
            break;
    }

    $index = mysql_query ( 'SELECT * FROM '.$global_config_arr['pref'].'counter_ref '.$query.'', $FD->sql()->conn() );
    $referrer_number = mysql_num_rows ( $index );
    if ( $referrer_number <= 0 ) {
    	echo'
                        <tr>
                            <td class="n" align="center" colspan="4">
                                '.$FD->text('page', 'referrer_no_entries').'
                            </td>
                        </tr>
		';
	}

	while ( $referrer_arr = mysql_fetch_assoc ( $index ) )
    {
        $dburlfull = $referrer_arr['ref_url'];

		$referrer_arr['ref_url'] = substr ( $referrer_arr['ref_url'], 7 );
		$referrer_maxlenght = 40;
		if (strlen($referrer_arr['ref_url']) > $referrer_maxlenght)
        {
            $referrer_arr['ref_url'] = substr($referrer_arr['ref_url'],0, $referrer_maxlenght) . '...';
        }

		$referrer_arr['ref_first'] = date('d.m.Y H:i', $referrer_arr['ref_first']);
        $referrer_arr['ref_last'] = date('d.m.Y H:i', $referrer_arr['ref_last']);

		if ( $referrer_arr['ref_url'] == '' ) {
            echo'
                        <tr>
                            <td class="n" align="left">
                                '.$FD->text('page', 'referrer_unknown').'
                            </td>
                            <td class="n" align="center">
                                '.$referrer_arr['ref_count'].'
                            </td>
                            <td class="n" align="center">
                                '.$referrer_arr['ref_first'].'
                            </td>
                            <td class="n" align="center">
                                '.$referrer_arr['ref_last'].'
                            </td>
                        </tr>
            ';
        } else {
			echo'
                        <tr>
                            <td class="n" align="left">
                                <a href="'.$dburlfull.'" style="text-decoration:none;" target="_blank" title="'.$dburlfull.'">
                                   '.$referrer_arr['ref_url'].'
                                </a>
                            </td>
                            <td class="n" align="center">
                                '.$referrer_arr['ref_count'].'
                            </td>
                            <td class="n" align="center">
                                '.$referrer_arr['ref_first'].'
                            </td>
                            <td class="n" align="center">
                                '.$referrer_arr['ref_last'].'
                            </td>
                        </tr>
			';
		}
	}

    echo'
					</table>
	';

////////////////////////
/// Referrer l�schen ///
////////////////////////

    echo'
                    <form action="" method="post">
                        <input type="hidden" value="stat_ref" name="go">
                        <table class="configtable" cellpadding="4" cellspacing="0">
							<tr><td class="space"></td></tr>
							<tr><td class="line" colspan="3">'.$FD->text('page', 'referrer_delete_title').'</td></tr>
							<tr>
                                <td class="config">
                                    '.$FD->text('page', 'referrer_delete_entries').'
                                </td>
                            </tr>
							<tr>
                                <td class="config">
                                    '.$FD->text('page', 'referrer_delete_with').'
                                    &nbsp;
                                    <select name="del_contact">
                                        <option value="first">'.$FD->text('page', 'referrer_first').'</option>
                                        <option value="last">'.$FD->text('page', 'referrer_last').'</option>
                                    </select>
                                    &nbsp;
                              		<select name="del_age">
                                        <option value="older">'.$FD->text('page', 'referrer_delete_older').'</option>
                                        <option value="younger">'.$FD->text('page', 'referrer_delete_younger').'</option>
                                    </select>
                                    &nbsp;
                                    <input class="text" name="del_days" size="3" maxlength="3" value="5">
                                    '.$FD->text('page', 'referrer_delete_days').'
                                </td>
                            </tr>
							<tr>
                                <td class="config">
                                    '.$FD->text('page', 'referrer_delete_and').'
                                    &nbsp;
                                    <select name="del_amount">
                                        <option value="less">'.$FD->text('page', 'referrer_delete_less').'</option>
                                        <option value="more">'.$FD->text('page', 'referrer_delete_more').'</option>
                                    </select>
                                    &nbsp;
                                    <input class="text" name="del_hits" size="3" maxlength="3" value="3">
                                    '.$FD->text('page', 'referrer_delete_hits').'.
                                </td>
                            </tr>
                            <tr><td class="space"></td></tr>
                            <tr>
                                <td class="buttontd">
                                    <button class="button_new" type="submit" name="delete_referrer" value="1">
                                        '.$FD->text('admin', 'button_arrow').' '.$FD->text('page', 'referrer_delete_button').'
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>
	';
}
?>
