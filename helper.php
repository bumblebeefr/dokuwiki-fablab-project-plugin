<?php
/**
 * DokuWiki Plugin fablab (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Bumblebee <>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

class helper_plugin_fablab extends DokuWiki_Plugin {

    function helper_plugin_fablab() {
		 if (plugin_isdisabled('tag')
                    || (!$this->taghelper = plugin_load('helper', 'tag'))) {
                msg("No plugin Tag is enabled", -1);
        }
	}
	
	function prefixTags($tags,$prefix){
			$ret = array();
			foreach ($tags as $tag) {
				if(trim($tag) !=""){
					array_push ($ret,$prefix.":".$tag);
				}
			}
			return $ret;
	}
	
	public function parseTags($prefix,$definition){
		return $this->prefixTags($this->taghelper->_parseTagList(preg_replace("/^".$prefix." ?:/i","",$definition)),$prefix);
		
	}
	
	function renderSomeDokuwiki($doc){


		// Create the parser
		$Parser = & new Doku_Parser();
		 
		// Add the Handler
		$Parser->Handler = & new Doku_Handler();
		 
		// Load all the modes
		$Parser->addMode('listblock',new Doku_Parser_Mode_ListBlock());
		$Parser->addMode('preformatted',new Doku_Parser_Mode_Preformatted()); 
		$Parser->addMode('notoc',new Doku_Parser_Mode_NoToc());
		$Parser->addMode('header',new Doku_Parser_Mode_Header());
		$Parser->addMode('table',new Doku_Parser_Mode_Table());
		 
		$formats = array (
			'strong', 'emphasis', 'underline', 'monospace',
			'subscript', 'superscript', 'deleted',
		);
		foreach ( $formats as $format ) {
			$Parser->addMode($format,new Doku_Parser_Mode_Formatting($format));
		}
		 
		$Parser->addMode('linebreak',new Doku_Parser_Mode_Linebreak());
		$Parser->addMode('footnote',new Doku_Parser_Mode_Footnote());
		$Parser->addMode('hr',new Doku_Parser_Mode_HR());
		 
		$Parser->addMode('unformatted',new Doku_Parser_Mode_Unformatted());
		$Parser->addMode('php',new Doku_Parser_Mode_PHP());
		$Parser->addMode('html',new Doku_Parser_Mode_HTML());
		$Parser->addMode('code',new Doku_Parser_Mode_Code());
		$Parser->addMode('file',new Doku_Parser_Mode_File());
		$Parser->addMode('quote',new Doku_Parser_Mode_Quote());
		 
		// These need data files. The get* functions are left to your imagination
		$Parser->addMode('acronym',new Doku_Parser_Mode_Acronym(array_keys(getAcronyms())));
		$Parser->addMode('smiley',new Doku_Parser_Mode_Smiley(array_keys(getSmileys())));
		$Parser->addMode('entity',new Doku_Parser_Mode_Entity(array_keys(getEntities())));
		 
		$Parser->addMode('multiplyentity',new Doku_Parser_Mode_MultiplyEntity());
		$Parser->addMode('quotes',new Doku_Parser_Mode_Quotes());
		 
		$Parser->addMode('camelcaselink',new Doku_Parser_Mode_CamelCaseLink());
		$Parser->addMode('internallink',new Doku_Parser_Mode_InternalLink());
		$Parser->addMode('media',new Doku_Parser_Mode_Media());
		$Parser->addMode('externallink',new Doku_Parser_Mode_ExternalLink());
		$Parser->addMode('emaillink',new Doku_Parser_Mode_EmailLink());
		$Parser->addMode('windowssharelink',new Doku_Parser_Mode_WindowsShareLink());
		$Parser->addMode('filelink',new Doku_Parser_Mode_FileLink());
		$Parser->addMode('eol',new Doku_Parser_Mode_Eol());
		 
		 
		// Get a list of instructions
		$instructions = $Parser->parse($doc);
		 
		// Create a renderer
		$Renderer = & new Doku_Renderer_XHTML();
		 
		# Load data like smileys into the Renderer here

		// Loop through the instructions
		foreach ( $instructions as $instruction ) {
		 
			// Execute the callback against the Renderer
			call_user_func_array(array(&$Renderer, $instruction[0]),$instruction[1]);
		}
		 
		// Display the output
		return $Renderer->doc;		
		
		
	}
	
}

// vim:ts=4:sw=4:et:
