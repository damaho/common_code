/* *********************************************************************
 * Copyright (C) 2007-2012 Kelly Stratton Music
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
  * 
* ****************************************************************** */
var JPlayerManager = {};

$(function() {
	function JPlayer(m_name) {
		var m_current_track = 0;
		var m_play_status = false;
		var m_tracks = [];
		var m_track_map = {};
		var m_supplied = {};
		var self = this;
		
		this.add_track = function(data) {
			if (data.uid == null || data.uid == undefined)
				return false;
			var presumed_index = m_tracks.length;
			m_supplied[data.format] = true;
			m_tracks.push(data);
			m_track_map[data.uid] = presumed_index;
		}
		
		this.update_play_status = function(p_status) {
			//m_play_status = !$('#jplayer__' + m_name).data().jPlayer.status.paused;
			m_play_status = p_status;
			this.change_play_button();
		}
		
		this.play_trackid = function(uid) {
			if (m_track_map[uid] == undefined)
				return false;
			self.play_track(m_track_map[uid]);
		}
		
		this.play_track = function(p_id) {
			m_play_status = true;
			self.change_track(p_id);
		}
		
		this.change_play_button = function() {
			if (!m_play_status) {
				$('#jplayer_play__' + m_name).removeClass('jplayer_pause').addClass('jplayer_play');
			} else {
				$('#jplayer_play__' + m_name).removeClass('jplayer_play').addClass('jplayer_pause');
			}
		}
		
		this.change_track = function(p_id) {
			this.set_track(p_id);
			this.config_track();
			if (m_play_status)
				this.do_play();
		}
		
		this.config_track = function() {
			var p_fmt = m_tracks[m_current_track].format;
			var p_file = m_tracks[m_current_track].file;
			var p_media = {};
			p_media[p_fmt] = p_file;
			$('#jplayer_info__' + m_name).html(m_tracks[m_current_track].description);
			$('#jplayer__' + m_name).jPlayer('setMedia',p_media);
		}
		
		this.do_play = function() {
			$('#jplayer__' + m_name).jPlayer('play');
			self.update_play_status(true);
		}
		
		this.do_pause = function() {
			$('#jplayer__' + m_name).jPlayer('pause');
			self.update_play_status(false);
		}
		
		this.do_stop = function() {
			$('#jplayer__' + m_name).jPlayer('stop');
			self.update_play_status(false);
		}

		this.set_track = function(p_id) {
			m_current_track = p_id;
		}
		
		this.play_next = function() {
			if (m_current_track + 1 >= m_tracks.length)
				return;
			this.change_track(m_current_track + 1);
		}

		this.play_prev = function() {
			if (m_current_track - 1 < 0)
				return;
			this.change_track(m_current_track - 1);
		}
		
		this.play_first = function() {
			this.change_track(0);
		}
		
		this.play_last = function() {
			this.change_track(m_tracks.length - 1);
		}
		
		this.click_play = function() {
			if (m_play_status)
				this.do_pause();
			else
				this.do_play();
		}
		
		this.ready_jplayer=function() {
			var p_supplied = '';
			for (var fmt in m_supplied) {
				if (p_supplied != '')
					p_supplied += ', ';
				p_supplied += fmt;
			}
			$('#jplayer__' + m_name).jPlayer({
				ready: function() {
					self.change_track(0);
				},
				swfPath: "/swf",
				supplied: p_supplied,
				oggSupport: false,
				wmode:"window"
			})
			.jPlayer('onSoundComplete', self.play_next);
		}
		
		this.init = function() {
			this.ready_jplayer();
			$('#jplayer_prev__' + m_name).click( function() { self.play_prev(); } );
			$('#jplayer_play__' + m_name).click( function() { self.click_play(); } );
			//$('#jplayer_pause__' + m_name).click( function() { self.do_pause(); } );
			//$('#jplayer_stop__' + m_name).click( function() { self.do_stop(); } );
			$('#jplayer_next__' + m_name).click( function() { self.play_next(); } );
		}
		
	}
	
	function manager() {
		var m_players = {};
		
		this.add_player = function(p_name) {
			m_players[p_name] = new JPlayer(p_name);
		}
		
		this.add_track = function(p_player_name, p_track_data) {
			m_players[p_player_name].add_track(p_track_data);
		}
		
		this.play_track = function(p_player,p_uid) {
			m_players[p_player].play_trackid(p_uid);
		}
		
		this.init_player = function(p_name) {
			m_players[p_name].init();
		}
	}
	
	JPlayerManager = new manager();
});

