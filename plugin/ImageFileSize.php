<?php
// Copyright 2013 Won-Kyu Park <wkpark at kldp.org>
// All rights reserved. Distributable under GPL see COPYING
// a ImageFileSize plugin for the MoniWiki
//
// Author: Won-Kyu Park <wkpark@gmail.com>
// Date: 2013-04-28
// Name: ImageFileSize
// Description: fetch the size of external images plugin
// URL: MoniWiki:ImageFileSizeMacro
// Version: $Revision: 1.0 $
// License: GPL
//
// Usage: [[ImageUrlSize(url)]]
//

function macro_ImageFileSize($formatter, $value = '', $params = array()) {
    global $Config;

    if (empty($value)) return '';

    $sz = 0;
    // check if it is valid or not
    while (preg_match('/^((?:https?|ftp):\/\/.*(\.(?:jpg|jpeg|gif|png)))(?:\?|&)?/i', $value, $m)) {
        $value = $m[1];

        // check the file size saved by the fetch plugin
        $si = new Cache_text('fetchinfo');

        if ($si->exists($value) && ($info = $si->fetch($value)) !== false) {
            $sz = $info['size'];
            break;
        }

        $sc = new Cache_text('imagefilesize');

        if (empty($params['.refresh']) and $sc->exists($value) and $sc->mtime($value) < time() + 60*60*24*20) {
            $sz = $sc->fetch($value);
        } else {
            // dynamic macro
            if ($formatter->_macrocache and empty($params['call']))
                return $formatter->macro_cache_repl('ImageFileSize', $value);
            if (empty($params['call']))
                $formatter->_dynamic_macros['@ImageFileSize'] = 1;

            // do not fetch the size of image right now. just fetch the cached info by the fetch plugin
            if (empty($params['call']) and !empty($Config['fetch_imagesize']) and $Config['fetch_imagesize'] == 2)
                return _("Unknown");

            require_once dirname(__FILE__).'/../lib/HTTPClient.php';

            $http = new HTTPClient();

            // set referrer
            $referer = '';
            if (!empty($Config['fetch_referer_re'])) {
                foreach ($Config['fetch_referer_re'] as $re=>$ref) {
                    if (preg_match($re, $value)) {
                        $referer = $ref;
                        break;
                    }
                }
            }

            // default referrer
            if (empty($referer) and !empty($Config['fetch_referer']))
                $referer = $Config['fetch_referer'];
            if (empty($referer))
                $referer = qualifiedUrl($formatter->link_url($formatter->page->urlname));

            $http->nobody = true;
            $http->referer = $referer;

            // check HEAD support for the internal fetch plugin
            $method = 'GET';
            if (!empty($Config['fetch_imagesize_head']) && strstr($value, $Config['fetch_action']))
                $method = 'HEAD';

            $http->sendRequest($value, array(), $method);

            if ($http->status != 200)
                return _("Unknown");

            if (isset($http->resp_headers['content-length']))
                $sz = $http->resp_headers['content-length'];
            $sc->update($value, $sz);
        }
        break;
    }
    if ($sz > 0) {
        $unit = array('Bytes', 'KB', 'MB', 'GB');
        for ($i = 0; $i < 4; $i++) {
          if ($sz <= 1024) {
            break;
          }
          $sz = $sz / 1024;
        }
        return round($sz, 2).' '.$unit[$i];
    }
    // set as dynamic macro or not.
    if ($formatter->_macrocache and empty($options['call']))
        return $formatter->macro_cache_repl('ImageFileSize', $value);
    if (empty($options['call']))
        $formatter->_dynamic_macros['@ImageFileSize'] = 1;
    return _("Unknown");
}

// for debug purpose
function do_imagefilesize($formatter, $params = array()) {
    if (!empty($formatter->refresh))
        $params['.refresh'] = true;
    $params['call'] = true;
    if (preg_match('@^(?:https?|ftp)://@', $params['url']))
        echo macro_ImageFileSize($formatter, $params['url'], $params);
}
// vim:et:sts=4:sw=4:
