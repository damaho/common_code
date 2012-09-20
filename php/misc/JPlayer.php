<?php
/* *********************************************************************
 * Copyright (C) 2007-2012 Dave Horn
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 *  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 *  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 *
 * $Id$
 * 
** ******************************************************************** */
require_once ('PageContent.php');

class JPlayer extends PageContent {
	private $player_id;
	private $name;
	private $media = array();
	private $supplied_formats = array();
	
	function __construct($id='default') {
		parent::__construct();
		$this->player_id = $id;
		$this->name = $id;
		$this->addJSFileTop('jquery.jplayer.min.js');
		$this->addJSFileTop('JPlayer.js');
		$this->addCSSFile('jplayer.css');

	}
	
	function getXHTML() {
		$xhtml  = "<div id='jplayer__{$this->player_id}'></div>\n";
		
		$xhtml .= "<div id='jplayer_interface__{$this->player_id}' class='jplayer_interface'>\n";
		
		
//		$xhtml .= " <div class='jplayer_control_container'>\n";
		$xhtml .= "	<ul id='jplayer_controls__{$this->player_id}' class='jplayer_controls'>\n";
		$xhtml .= "		<li id='jplayer_prev__{$this->player_id}' class='jplayer_prev'>&laquo;</li>\n";
		$xhtml .= "		<li id='jplayer_play__{$this->player_id}' class='jplayer_play'>Play</li>\n";
		//$xhtml .= "		<li id='jplayer_pause__{$this->player_id}' class='jplayer_pause'>Pause</li>\n";
		//$xhtml .= "		<li id='jplayer_stop__{$this->player_id}' class='jplayer_stop'>Stop</li>\n";
		$xhtml .= "		<li id='jplayer_next__{$this->player_id}' class='jplayer_next'>&raquo;</li>\n";
		$xhtml .= "	</ul>\n";
//		$xhtml .= " </div>\n";

		$xhtml .= "	<div id='jplayer_info__{$this->player_id}' class='jplayer_song_info'></div>\n";
		
		//$xhtml .= "	<div id='jplayer_progress__{$this->player_id}' class='jplayer_progress'>\n";
		//$xhtml .= "		<div id='jplayer_load_bar__{$this->player_id}' class='jplayer_load_bar'>\n";
		//$xhtml .= "			<div id='jplayer_play_bar__{$this->player_id}' class='jplayer_play_bar'></div>\n";
		//$xhtml .= "		</div>\n";
		//$xhtml .= "	</div>\n";
		
		
		$xhtml .= "</div>\n";
		
		return $xhtml;
	}
	
	function addMedia($description,$filename,$format, $unique_name) {
		$host = $_SERVER['HTTP_HOST'];
		$this->media[] = array('description'=>$description, 'filename'=>'http://' . $host . '/' . $filename,'format' => $format, 'uid' => $unique_name);
		$this->supplied_formats[$format] = true;
	}
	
	private function getMedia() {
		return $this->media;
	}
	
	function getJSBottom() {
		$js = "";
		$js .= "JPlayerManager.add_player('{$this->player_id}');\n";
		foreach ($this->media as $idx => $data) {
			$js .= "JPlayerManager.add_track('{$this->player_id}',{description: '{$data['description']}', file : '{$data['filename']}', format: '{$data['format']}', uid: '{$data['uid']}'});\n";
		}
		$js .= "JPlayerManager.init_player('{$this->player_id}');\n";
		return $js;
	}
}

?>
