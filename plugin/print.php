<?php
// Copyright 2003 by Won-Kyu Park <wkpark at kldp.org>
// All rights reserved. Distributable under GPL see COPYING
// a print action plugin for the MoniWiki
//
// $Id$

function do_print($formatter,$options) {
  global $DBInfo;
  $options['css_url']=$DBInfo->url_prefix."/css/print.css";
  $formatter->send_header("",$options);
  print "<div id='printHeader'>";
  print "<h2>$options[page]</h2>";
  print "</div>";
  print "<div id='wikiContent'>";
  $formatter->external_on=1;
  $formatter->send_page();
  print "</div></div>";
  print "<div id='printFooter'>";
  print sprintf(_("Retrieved from %s"),
    qualifiedUrl($formatter->link_url($formatter->page->name))).'<br/>';
  if ($mtime=$formatter->page->mtime()) {
    $lastedit=date("Y-m-d",$mtime);
    $lasttime=date("H:i:s",$mtime);
    print sprintf(_("last modified %s %s"),$lastedit,$lasttime);
  }
  print "</div></body></html>";
  return;
}

// vim:et:sts=2:
?>
