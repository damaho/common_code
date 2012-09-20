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
 * Requires lightwindow (jquery)
 * 
 * alum__<name>__container - holds all the photos
 * 
* ****************************************************************** */
PhotoManager = {};
$(function() {
	var album = function(m_name) {
		var photos = {};
		var is_loaded = false;
		
		this.add_photo = function(id,filename,caption,thumb_file) {
			photos[id] = {'file' : filename, 'caption' : caption, 'thumb' : thumb_file};
		}
		
		this.open = function() {
			// div holding photos opens
			$('#album__' + m_name + '__container').slideDown('fast',function() {});
		}
		
		this.close = function() {
			// div holding photos closes
			$('#album__' + m_name + '__container').slideUp('fast',function() {});
		}
		
		this.load = function() {
			for (var id in photos) {
				// add thumbnail to containing node
			}
			is_loaded = true;
		}
		
		this.unload = function() {
			this.close();
			$('#album__' + m_name + '__container').empty();
			is_loaded = false;
		}
	}
	
	var album_manager = function() {
		var albums = {};
		
		this.isset = function(p_name) {
			if (albums[p_name] == undefined)
				return false;
			return true;
		}
		
		this.add_photo = function(p_name,id,filename,caption,thumb_file) {
			if (this.isset(p_name))
				albums[p_album].add_photo(id,filename,caption,thumb_file);
		}
		
		this.add_album = function(p_name) {
			albums[p_name] = new album(p_name);
		}
		
		this.open_album = function(p_name) {
			if (this.isset(p_name))
				albums[p_name].open;
		}
		
		this.close_album = function(p_name) {
			if (this.isset(p_name))
				albums[p_name].close();
		}
	}
	
	PhotoManager = new album_manager();
	
});
