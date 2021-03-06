<?php
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
 * $Id: PageLibs.php 65 2012-09-23 17:00:02Z Dave $
 * 
 * ****************************************************************** */
class PageLibs {
	public function get_start_html($encoding="ISO-8859-1") {
		$xhtml = "";
		$xhtml .= "<?xml version=\"1.0\" encoding=\"$encoding\"?>\n";
		$xhtml .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n"; 
 		$xhtml .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xhtml .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
		return $xhtml;
	}
	
	public function get_end_html() {
		$xhtml = "";
		$xhtml .= "</html>\n";
		return $xhtml;
	}
	
	public function get_head_start() {
		$xhtml = "<head>\n";
		return $xhtml;
	}
	
	public function get_head_end() {
		$xhtml = "</head>\n";
		return $xhtml;
	}

	public function get_body_start($extra='') {
		$xhtml = "<body $extra>\n";
		return $xhtml;
	}
	
	public function get_body_end() {
		$xhtml = "</body>\n";
		return $xhtml;
	}
}


?>
