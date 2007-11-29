<?php
// Copyright 2006 Won-Kyu Park <wkpark at kldp.org>
// All rights reserved. Distributable under GPL see COPYING
// a sample plugin for the MoniWiki
//
// Usage: [[Test]]
//
// $Id$

function macro_InputForm($formatter,$value,$options=array()) {
    $out='';
    $type='select';
    $name='val[]';

    if (empty($value)) return "</form>\n";
    if (strpos($value,':')!==false)
        list($type,$value)=explode(':',$value,2);

    if (!in_array($type,array('form','select','input','submit','checkbox','radio')))
        $type='select';

    $myname=$name;
    switch($type) {
    case 'form':
        list($method,$action,$dum)=explode(':',$value);
        $method= in_array(strtolower($method),array('post','get')) ? $method:'get';
        $url=$formatter->link_url($formatter->page->urlname);
        $out="<form method='$method' action='$url'>\n".
            "<input type='hidden' name='action' value='$action' />\n";
        break;
    case 'submit':
        $out.="<input type='$type' name='$name' value='$value' />\n";
        break;
    case 'input':
        list($myname,$size,$value)=explode(':',$value,3);
        $size=$size ? "size='$size'":'';
        $out.="<input type='$type' {$size}name='$myname' value='$value' />\n";
        break;
    case 'select':
    default:
        list($myname,$value)=explode(':',$value);
        $list=explode(',',$value);

        $out.='<option>----</option>'."\n";
        foreach ($list as $l) {
            $l=trim($l);
            if (($p=strrpos($l,' ')) !== false and substr($l,$p+1) == 1) {
                $check=' selected="selected"';
                $l=substr($l,0,-1);
            } else $check='';
            $out.="<option value='".$l."'$check>"._($l)."</option>\n";
        }
        $out="<select name='$myname'>".$out."</select>\n";
        break;
    }

    return $out;
}

// vim:et:sts=4:sw=4
?>
