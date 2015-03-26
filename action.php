<?php
/**
 * Fablab-Plugin: Renders the page's toc inside the  content
 *
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'action.php');

class action_plugin_fablab extends DokuWiki_Action_Plugin {

    /**
     * Register event handlers
     */
    function register(&$controller) {
        $controller->register_hook('RENDERER_CONTENT_POSTPROCESS', 'AFTER', $this, 'handle_renderer_content_postprocess', array());
    }
   
    /**
     * Replace our placeholder with the actual toc content
     */
    function handle_renderer_content_postprocess(&$event, $param) {
        global $TOC;
        if ($TOC) {
			$html = '<br/><h3 class="toggle">Table des matères</h3>';
            $html .= html_buildlist($TOC, 'inlinetoc2', 'fablab_toc_li') ;
            $event->data[1] = str_replace('<!-- TOCPLACEHOLDER -->',
                                          $html,
                                          $event->data[1]);
                                       
        }
    }
   

}

/**
 * Callback for html_buildlist.
 * Builds list items with inlinetoc2 printable class instead of dokuwiki's toc class which isn't printable.
 */
function fablab_toc_li($item){
    if(isset($item['hid'])){
        $link = '#'.$item['hid'];
    }else{
        $link = $item['link'];
    }

    return '<span class="li"><a href="'.$link.'">'. hsc($item['title']).'</a></span>';
}
