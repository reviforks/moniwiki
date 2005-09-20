<?php
// Copyright 2005 Won-Kyu Park <wkpark at kldp.org>
// All rights reserved. Distributable under GPL see COPYING
// a Tour plugin for the MoniWiki
//
// Usage: [[Tour]]
//
// $Id$

if (!function_exists('do_dot'))
    if ($pn=getPlugin('dot')) include_once("plugin/$pn.php");

function do_tour($formatter,$options) {
    #header("Content-Type: text/plain");
    $formatter->send_header('',$options);
    $formatter->send_title(sprintf(_("Tour from %s"),$options['page']),'',
        $options);

    print macro_Tour($formatter,$options['page'],$options);
    //$args['editable']=1;
    $formatter->send_footer($args,$options);
}

function macro_Tour($formatter,$value,$options=array()) {
    global $DBInfo;

define(TOUR_LEAFCOUNT,4);
define(TOUR_DEPTH,3);

    if (!$value) $value=$formatter->page->name;

    if ($options['w'] and $options['w'] < 10) $count=$options['w'];
    else $count=TOUR_LEAFCOUNT;
    if ($options['d'] and $options['d'] < 7) $depth=$options['d'];
    else $depth=TOUR_DEPTH;

    $color=array();
    makeTree($value,$node,$color,$depth,$count);
    if (!$node) $node=array($value=>array());

    $allnode=array_keys($node);
    asort($allnode);

    $id=0;
    $outs=array();
    while (list($leafname,$leaf) = @each ($node)) {
        if (!$leafs[$leafname]) {
            $urlname=_rawurlencode($leafname);
            $leafs[$leafname]=1;
            $url[$leafname]=$urlname;
        }
        $selected=array_intersect($node[$leafname],$allnode);
        asort($selected);
        foreach ($selected as $leaf) {
            if (!$leafs[$leaf]) {
                $urlname=_rawurlencode($leaf);
                $url[$leaf]=$urlname;
                $id=$leafs[$leaf]=$leafs[$leafname]+1;
                if (!$outs[$id]) $outs[$id]=array();
                $outs[$id][]= $leaf;
            }
        }
    }
    unset($out[0]);
    $wide= $formatter->link_tag($url[$value],
        "?action=tour&amp;w=".($count+1)."&amp;d=$depth",_("links"));
    $deep= $formatter->link_tag($url[$value],
        "?action=tour&amp;w=$count&amp;d=".($depth+1),_("deeper"));
    $link='<h3>'.sprintf(_("More %s or more %s"),$wide,$deep).'</h3>';

    foreach ($allnode as $node) {
        $pages.='<li>'.$formatter->link_tag($url[$node],"",$node)."</li>\n";
    }
    $title='<h3>'.sprintf(_("Total %d related pages"),sizeof($allnode)).'</h3>';

    $out=array();
    $dep=1;
    foreach ($outs as $ls) {
        asort($ls);
        $temp='';
        foreach ($ls as $leaf) {
            $temp.= ' <li>'.$formatter->link_tag($url[$leaf],
                "?action=tour",$leaf)."</li>\n";
        }
        $out[]="<ul class='depth-$dep'>".$temp.'</ul>';
        $dep++;
    }
    $ret=implode("\n",$out);
    $ret='<div class="tourLeft">'.$link.$ret.'</div>'.
        "<div class=\"tourRight\">$title<ol>$pages</ol></div>\n".
        "<div class=\"tourFoot\"></div>\n";

    return '<div class="wikiTour">'.$ret."</div>\n";
}

// vim:et:sts=4:
?>
