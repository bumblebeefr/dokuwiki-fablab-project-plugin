<?php
/**
 * DokuWiki Plugin fablab (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Bumblebee <>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_fablab_project extends DokuWiki_Syntax_Plugin {
    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getSort() {
        return 306;
    }


    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{\{project.*?\}\}\}',$mode,'plugin_fablab_project');
    }

    public function handle($match, $state, $pos, &$handler){
         if (plugin_isdisabled('tag')
                    || (!$taghelper = plugin_load('helper', 'tag'))) {
                msg("No plugin Tag is enabled", -1);
        }
        
        $projecthelper = plugin_load('helper', 'fablab');
        
        $data = array();
        $data['tags'] = array();
        $data['materiaux']  = array();
        $data['usager']  = array();
        $data['machines']  = array();
        $data['technologies']  = array();
        $data['langages']  = array();
        $data['licence']  = array();
        $data['logiciels']  = array();
        $data['liens'] = "";
        $data['sources'] = array();
        
        
		$defintions = explode("\n",$match);
		foreach($defintions as $defintion){
				$definition = trim($defintion);
				if(preg_match("/^tags ?:/i",$definition)){
						$data['tags'] += $projecthelper->parseTags("tags",$definition);
				}else if(preg_match("/^materiaux ?:/i",$definition)){
						$data['materiaux'] += $projecthelper->parseTags("materiaux",$definition);	
				}else if(preg_match("/^usager ?:/i",$definition)){
						$data['usager'] += $projecthelper->parseTags("usager",$definition);	
				}else if(preg_match("/^machines ?:/i",$definition)){
						$data['machines'] += $projecthelper->parseTags("machines",$definition);
				}else if(preg_match("/^technologies ?:/i",$definition)){
						$data['technologies'] += $projecthelper->parseTags("technologies",$definition);
				}else if(preg_match("/^logiciels ?:/i",$definition)){
						$data['logiciels'] += $projecthelper->parseTags("logiciels",$definition);
				}else if(preg_match("/^langages ?:/i",$definition)){
						$data['langages'] += $projecthelper->parseTags("langages",$definition);
				}else if(preg_match("/^liens ?:/i",$definition)){
					$data['liens'] = preg_replace("/^liens ?:/i","",$definition);
				}else if(preg_match("/^sources ?:/i",$definition)){
					$arr = explode(" ",preg_replace("/^sources ?:/i","",$definition));
					foreach ($arr as &$value) {
						$v = $this->parseMediaLink($value);
						if( $v != ""){
							array_push ($data['sources'],$v);
						}
					}
				}else if(preg_match("/^picture ?:/i",$definition)){
					$data['picture'] = $this->parseMediaLink($definition);
				}else if(preg_match("/^licence ?:/i",$definition)){
						$data['licence'] += $projecthelper->parseTags("licence",$definition);
				}
		}
		
        return $data;
    }
    
    function parseMediaLink($definition){
		$pict = explode("|",preg_replace("/^picture ?:/i","",$definition));
		$pict = $pict[0];
		$pict = explode("?",$pict);
		$pict = $pict[0];
		$pict = preg_replace("/\}\}/i","",$pict);
		$pict = preg_replace("/\{\{/i","",$pict);
		return trim($pict);
	}

	public function renderTag($taghelper,$renderer,$data, $tagname,$libelle){
		$tags = $taghelper->tagLinks($data[$tagname]);
		if ($tags){
			$renderer->doc .= '<li><label>'.$libelle.' : </label>'.DOKU_LF.
			DOKU_TAB.$tags.DOKU_LF.
			'</li>'.DOKU_LF;
		}
	}

    public function render($mode, &$renderer, $data) {
		global $ID;
       //print_r($renderer);
       if ($data === false) return false;
        /** @var helper_plugin_tag $taghelper */
         if (plugin_isdisabled('tag')
                    || (!$taghelper = plugin_load('helper', 'tag'))) {
                msg("No plugin Tag is enabled", -1);
        }
        
        if (!$fablabhelper =& plugin_load('helper', 'fablab')) return false;
		  //$renderer->info['toc'] = false;
		
        // XHTML output
        if ($mode == 'xhtml') {
			$renderer->doc .= '<div id="projectBox"  class="projectBox">';
			$renderer->doc .= '<h3 class="toggle open" style="cursor: pointer;">Fiche projet</h3>';
			$renderer->doc .= '<div style="">';
			
			
            if (array_key_exists('picture',$data) && trim($data['picture']) != ""){
				$renderer->doc .= '<div style="text-align:center" class="projectBoxPicture">';
				$renderer->doc .= $fablabhelper->renderSomeDokuwiki("{{:".$data['picture']."?250|}}");
				$renderer->doc .= '</div>';
			}
			
			$renderer->doc .= '<ul class="toc">';

			$this->renderTag($taghelper,$renderer,$data,'materiaux','Materiaux');
			$this->renderTag($taghelper,$renderer,$data,'machines','Machines');
			$this->renderTag($taghelper,$renderer,$data,'logiciels','Logiciels');
			$this->renderTag($taghelper,$renderer,$data,'technologies','Technologies');
			$this->renderTag($taghelper,$renderer,$data,'langages','Languages');
			$this->renderTag($taghelper,$renderer,$data,'usager','Réalisé par');
			$this->renderTag($taghelper,$renderer,$data,'licence','Licence');
			$this->renderTag($taghelper,$renderer,$data,'tags','Tags');
			
			if(trim($data['liens']) != ""){
				$renderer->doc .= '<li><label>Liens : </label>'.DOKU_LF. DOKU_TAB;
				$renderer->doc .= $fablabhelper->renderSomeDokuwiki(trim($data['liens']));
				$renderer->doc .= $tags.DOKU_LF.'</li>'.DOKU_LF;
			}
  
			if(count($data['sources']) >0){
				$renderer->doc .= '<li><label>Sources : </label>'.DOKU_LF. DOKU_TAB;
				$i = 0;
				foreach ($data['sources'] as &$source) {
					if($i>0){
						$renderer->doc .= ", ";
					}
					$name = explode(":",$source);
					$name = $name[count($name)-1];
					$renderer->doc .= '<a href="'.ml($source).'" target="_blank" download="'.$name.'">';
					$renderer->doc .= $name;						
					$renderer->doc .= '</a>';
					$i++;
				}
				$renderer->doc .= $tags.DOKU_LF.'</li>'.DOKU_LF;
			}
            
            $renderer->doc .= '</ul>';
            
            //$renderer->doc .= $fablabhelper->renderSomeDokuwiki("**test** //coucou//");
            
            $renderer->doc .= "<!-- TOCPLACEHOLDER -->";
            $renderer->doc .= '</div>';   
            $renderer->doc .= '</div>';   
            
            return true;

        // for metadata renderer
        } elseif ($mode == 'metadata') {
          

            if (!isset($renderer->meta['subject'])) $renderer->meta['subject'] = array();

            // each registered tags in metadata and index should be valid IDs
            $data['tags'] = array_map('cleanID', $data['tags']);
            $data['materiaux'] = array_map('cleanID', $data['materiaux']);
            $data['machines'] = array_map('cleanID', $data['machines']);
            $data['logiciels'] = array_map('cleanID', $data['logiciels']);
            $data['technologies'] = array_map('cleanID', $data['technologies']);
            $data['langages'] = array_map('cleanID', $data['langages']);
            $data['usager'] = array_map('cleanID', $data['usager']);
            $data['licence'] = array_map('cleanID', $data['licence']);
            
            
            // merge with previous tags and make the values unique
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['materiaux']));
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['machines']));
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['tags']));
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['logiciels']));
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['langages']));
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['technologies']));
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['usager']));
            $renderer->meta['subject'] = array_unique(array_merge($renderer->meta['subject'], $data['licence']));


            if ($renderer->capture) $renderer->doc .= DOKU_LF.implode(' ', $data['tags']).DOKU_LF;

            // add references if tag page exists
            foreach ($data['tags'] as $tag) {
                resolve_pageid($taghelper->namespace, $tag, $exists); // resolve shortcuts
                $renderer->meta['relation']['references'][$tag] = $exists;
            }

            return true;
        }

        return false;
    }
}

// vim:ts=4:sw=4:et:
