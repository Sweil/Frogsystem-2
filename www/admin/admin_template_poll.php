<?php

    $TEMPLATE_GO = 'tpl_poll';
    $TEMPLATE_FILE = '0_polls.tpl';
    $TEMPLATE_EDIT = null;

    $tmp['name'] = 'APPLET_POLL_ANSWER_LINE';
    $tmp['title'] = $admin_phrases['template']['poll_line']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_line']['description'];
    $tmp['rows'] = '15';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'answer';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_line']['help_1'];
        $tmp['help'][1]['tag'] = 'answer_id';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_line']['help_2'];
        $tmp['help'][2]['tag'] = 'type';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_line']['help_3'];
        $tmp['help'][3]['tag'] = 'multiple';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_line']['help_4'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);

    $tmp['name'] = 'APPLET_POLL_BODY';
    $tmp['title'] = $admin_phrases['template']['poll_body']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_body']['description'];
    $tmp['rows'] = '20';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'question';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_body']['help_1'];
        $tmp['help'][1]['tag'] = 'answers';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_body']['help_2'];
        $tmp['help'][2]['tag'] = 'poll_id';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_body']['help_3'];
        $tmp['help'][3]['tag'] = 'type';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_body']['help_4'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);

    $tmp['name'] = 'APPLET_NO_POLL';
    $tmp['title'] = $admin_phrases['template']['poll_no_poll']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_no_poll']['description'];
    $tmp['rows'] = '10';
    $tmp['cols'] = '66';
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);



    $tmp['name'] = 'APPLET_RESULT_ANSWER_LINE';
    $tmp['title'] = $admin_phrases['template']['poll_result_line']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_result_line']['description'];
    $tmp['rows'] = '15';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'answer';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_result_line']['help_1'];
        $tmp['help'][1]['tag'] = 'votes';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_result_line']['help_2'];
        $tmp['help'][2]['tag'] = 'percentage';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_result_line']['help_3'];
        $tmp['help'][3]['tag'] = 'bar_width';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_result_line']['help_4'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);

    $tmp['name'] = 'APPLET_RESULT_BODY';
    $tmp['title'] = $admin_phrases['template']['poll_result']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_result']['description'];
    $tmp['rows'] = '20';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'question';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_result']['help_1'];
        $tmp['help'][1]['tag'] = 'answers';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_result']['help_2'];
        $tmp['help'][2]['tag'] = 'all_votes';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_result']['help_3'];
        $tmp['help'][3]['tag'] = 'participants';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_result']['help_4'];
        $tmp['help'][4]['tag'] = 'type';
        $tmp['help'][4]['text'] = $admin_phrases['template']['poll_result']['help_5'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);



    $tmp['name'] = 'LIST_LINE';
    $tmp['title'] = $admin_phrases['template']['poll_list_line']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_list_line']['description'];
    $tmp['rows'] = '15';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'question';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_list_line']['help_1'];
        $tmp['help'][1]['tag'] = 'url';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_list_line']['help_2'];
        $tmp['help'][2]['tag'] = 'all_votes';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_list_line']['help_3'];
        $tmp['help'][3]['tag'] = 'participants';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_list_line']['help_4'];
        $tmp['help'][4]['tag'] = 'type';
        $tmp['help'][4]['text'] = $admin_phrases['template']['poll_list_line']['help_5'];
        $tmp['help'][5]['tag'] = 'start_date';
        $tmp['help'][5]['text'] = $admin_phrases['template']['poll_list_line']['help_6'];
        $tmp['help'][6]['tag'] = 'end_date';
        $tmp['help'][6]['text'] = $admin_phrases['template']['poll_list_line']['help_7'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);

    $tmp['name'] = 'LIST_BODY';
    $tmp['title'] = $admin_phrases['template']['poll_list']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_list']['description'];
    $tmp['rows'] = '20';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'polls';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_list']['help_1'];
        $tmp['help'][1]['tag'] = 'order_question';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_list']['help_2'];
        $tmp['help'][3]['tag'] = 'order_all_votes';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_list']['help_4'];
        $tmp['help'][5]['tag'] = 'order_participants';
        $tmp['help'][5]['text'] = $admin_phrases['template']['poll_list']['help_6'];
        $tmp['help'][7]['tag'] = 'order_type';
        $tmp['help'][7]['text'] = $admin_phrases['template']['poll_list']['help_8'];
        $tmp['help'][9]['tag'] = 'order_start_date';
        $tmp['help'][9]['text'] = $admin_phrases['template']['poll_list']['help_10'];
        $tmp['help'][11]['tag'] = 'order_end_date';
        $tmp['help'][11]['text'] = $admin_phrases['template']['poll_list']['help_12'];

        $tmp['help'][2]['tag'] = 'arrow_question';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_list']['help_3'];
        $tmp['help'][4]['tag'] = 'arrow_all_votes';
        $tmp['help'][4]['text'] = $admin_phrases['template']['poll_list']['help_5'];
        $tmp['help'][6]['tag'] = 'arrow_participants';
        $tmp['help'][6]['text'] = $admin_phrases['template']['poll_list']['help_7'];
        $tmp['help'][8]['tag'] = 'arrow_type';
        $tmp['help'][8]['text'] = $admin_phrases['template']['poll_list']['help_9'];
        $tmp['help'][10]['tag'] = 'arrow_start_date';
        $tmp['help'][10]['text'] = $admin_phrases['template']['poll_list']['help_11'];
        $tmp['help'][12]['tag'] = 'arrow_end_date';
        $tmp['help'][12]['text'] = $admin_phrases['template']['poll_list']['help_13'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);


    $tmp['name'] = 'ANSWER_LINE';
    $tmp['title'] = $admin_phrases['template']['poll_main_line']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_main_line']['description'];
    $tmp['rows'] = '15';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'answer';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_main_line']['help_1'];
        $tmp['help'][1]['tag'] = 'votes';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_main_line']['help_2'];
        $tmp['help'][2]['tag'] = 'percentage';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_main_line']['help_3'];
        $tmp['help'][3]['tag'] = 'bar_width';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_main_line']['help_4'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);

    $tmp['name'] = 'BODY';
    $tmp['title'] = $admin_phrases['template']['poll_main_body']['title'];
    $tmp['description'] = $admin_phrases['template']['poll_main_body']['description'];
    $tmp['rows'] = '20';
    $tmp['cols'] = '66';
        $tmp['help'][0]['tag'] = 'question';
        $tmp['help'][0]['text'] = $admin_phrases['template']['poll_main_body']['help_1'];
        $tmp['help'][1]['tag'] = 'answers';
        $tmp['help'][1]['text'] = $admin_phrases['template']['poll_main_body']['help_2'];
        $tmp['help'][2]['tag'] = 'all_votes';
        $tmp['help'][2]['text'] = $admin_phrases['template']['poll_main_body']['help_3'];
        $tmp['help'][3]['tag'] = 'participants';
        $tmp['help'][3]['text'] = $admin_phrases['template']['poll_main_body']['help_4'];
        $tmp['help'][4]['tag'] = 'type';
        $tmp['help'][4]['text'] = $admin_phrases['template']['poll_main_body']['help_5'];
        $tmp['help'][5]['tag'] = 'start_date';
        $tmp['help'][5]['text'] = $admin_phrases['template']['poll_main_body']['help_6'];
        $tmp['help'][6]['tag'] = 'end_date';
        $tmp['help'][6]['text'] = $admin_phrases['template']['poll_main_body']['help_7'];
    $TEMPLATE_EDIT[] = $tmp;
    unset($tmp);


echo templatepage_init ($TEMPLATE_EDIT, $TEMPLATE_GO, $TEMPLATE_FILE);
?>
