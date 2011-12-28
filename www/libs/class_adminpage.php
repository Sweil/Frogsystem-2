<?php

/**
 * @file     class_adminpage.php
 * @folder   /libs
 * @version  0.2
 * @author   Satans Kr�melmonster, Sweil
 *
 * provides functions to manage admin-cp display-issues
 */
include_once(FS2_ROOT_PATH."libs/class_adminpage_children.php");

class adminpage{
  protected $tpl    = array();
  protected $cond   = array();
  protected $text   = array();
  protected $lang   = array();
  protected $common = array();
  protected $analysed = null;

  function __construct($pagefile){
    global $global_config_arr;
    $file_name = substr($pagefile, 0, -4);
    if(is_readable(FS2_ROOT_PATH."admin/template/".$file_name.".tpl"))
      $this->searchTpls(file_get_contents(FS2_ROOT_PATH."admin/template/".$file_name.".tpl"));

    if(is_readable(FS2_ROOT_PATH."lang/".$global_config_arr['language_text']."/admin/".$file_name.".txt"))
      $this->getLang(file_get_contents(FS2_ROOT_PATH."lang/".$global_config_arr['language_text']."/admin/".$file_name.".txt"));

    if(is_readable(FS2_ROOT_PATH."lang/".$global_config_arr['language_text']."/admin.txt"))
      $this->getCommon(file_get_contents(FS2_ROOT_PATH."lang/".$global_config_arr['language_text']."/admin.txt"));
  }

  public function addCond($name, $value){
    $this->cond[$name] = $value;
  }

  public function clearCond(){
    unset($this->cond);
    $this->cond = array();
  }

  public function addText($name, $value){
    $this->text[$name] = $value;
  }

  public function clearTexts(){
    unset($this->text);
    $this->text = array();
  }

  public function langValue($name){
    if(array_key_exists($name, $this->lang))
      return $this->lang[$name];
    else
      return "Localize: LANG::".$name;
  }

  private function commonValue($name){
    if(array_key_exists($name, $this->common))
      return $this->common[$name];
    else
      return "Localize: COMMON::".$name;
  }


  public function get($tplname, $clearempty = true){
    if(array_key_exists($tplname, $this->tpl)){
      // analyse string
      $this->analyse($this->tpl[$tplname]);
      // replace conditions
      $tmpval = $this->analysed->get($this->cond);

      // replace texts
      foreach($this->text as $key => $value){
        $tmpval = str_replace("<!--TEXT::$key-->", $value, $tmpval);
      }
      // replace language
      $tmpval = preg_replace ( "/<!--LANG::([^>]*)-->/e", '$this->langValue(\'$1\')', $tmpval);
      // replace common
      $tmpval = preg_replace ( "/<!--COMMON::([^>]*)-->/e", '$this->commonValue(\'$1\')', $tmpval);

      if($clearempty == true){
        $tmpval = preg_replace("#<!--TEXT::[^>]+-->#is", "", $tmpval);
      }
    } else die("template does not exist!");

    $this->clearCond();
    $this->clearTexts();

    return $tmpval;
  }

  protected function searchTpls($tplcontents){
    preg_match_all("#<!--DEF::([a-z-_]+)-->(.*?)<!--ENDDEF-->#is", $tplcontents, $dev);
    for($i=0;$i<count($dev[1]);$i++){
      $this->tpl[$dev[1][$i]] = $dev[2][$i];
    }
    unset($dev);
  }

  protected function getLangData($string){
      $string = str_replace(array("\r\n", "\r"), "\n", $string); // unify linebreaks
      $string = explode("\n", $string);

      $langData = array();
      foreach($string as $str){
          if(substr($str,0,1) !== "#"){ // line is not a comment
              preg_match("#([a-z0-9_]+?)\s*?:\s*(.*)#is", $str, $match);
              $langData[$match[1]] = $match[2];
              unset($match);
          }
      }
      return $langData;
  }

  protected function getLang($string){
    $this->lang = $this->getLangData($string);
  }

  protected function getCommon($string){
    $this->common = $this->getLangData($string);
  }

  protected function analyse($string, $parent = -1, $result = array(array(), array()), $type=0, $inif = array(-1 => false)){
    preg_match("#<!--IF::(.+?)\?(.+?)-->#is", $string, $if, PREG_OFFSET_CAPTURE);
    preg_match("#<!--ELSE-->#is", $string, $else, PREG_OFFSET_CAPTURE);
    preg_match("#<!--ENDIF-->#is", $string, $endif, PREG_OFFSET_CAPTURE);
    $iflength     = isset($if[0][0])    ? strlen($if[0][0])     : 0;
    $cond         = isset($if[1][0])    ? $if[1][0]             : "";
    $condval      = isset($if[2][0])    ? $if[2][0]             : "";
    $if           = isset($if[0][1])    ? $if[0][1]             : strlen($string);
    $elselength   = isset($else[0][0])  ? strlen($else[0][0])   : 0;
    $else         = isset($else[0][1])  ? $else[0][1]           : strlen($string);
    $endiflength  = isset($endif[0][0]) ? strlen($endif[0][0])  : 0;
    $endif        = isset($endif[0][1]) ? $endif[0][1]          : strlen($string);

    if($endif < $else && $endif < $if){ // ending if
      $result[1][] = array( 'parent'  => $parent,
                            'content' => substr($string, 0, $endif),
                            'type'    => $type,
                            'used'    => false
                          );

      $string = substr($string, $endiflength+$endif);
      $k = count($result[1])-1;
      while($k > 0 && $result[1][$k][parent] >= $result[1][count($result[1])-1][parent])
        $k--;
      $type   = $result[1][$k][type];
      $parent = $result[1][$k][parent];

    } elseif($else < $endif && $else < $if){ // else
      $result[1][] = array( 'parent'  => $parent,
                            'content' => substr($string, 0, $else),
                            'type'    => $type,
                            'used'    => false
                          );

      $string = substr($string, $else+$elselength);
      $type   = 2;
      $inif[$parent] = false;
    } elseif($if < $endif && $if < $else){ // if

      $result[1][] = array( 'parent'  => $parent,
                            'content' => substr($string, 0, $if),
                            'type'    => $type,
                            'used'    => false
                          );

      $result[0][] = array( 'cond'    => $cond,
                            'condval' => $condval,
                            'parent'  => $parent,
                            'inif'    => $inif[$parent]
                        );
      $inif[count($inif)-1] = true;
      $parent = count($result[0])-1;
      $type   = 1;
      $string = substr($string, $iflength+$if);
    } else { // end of string
      //print_r($result[1]); exit;
      $result[1][] = array( 'parent'  => $parent,
                            'content' => $string,
                            'type'    => 0,
                            'used'    => false
                          );
      $result[] = -1;
      sort($result);
      $this->analysed = new adminpage_condition($result);
      return;
    }

    $this->analyse($string, $parent, $result, $type, $inif);
  }
}
?>