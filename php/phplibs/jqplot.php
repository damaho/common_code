<?php
/* *********************************************************************
 * Copyright (C) 2007-2013 Dave Horn
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
 * ********************************************************************* */
require_once 'autoload.php';

class jqplot extends PageContent {
	function __construct() {
		$this->addJSFileTop("excanvas.min.js");
		$this->addJSFileTop("jquery.jqplot.min.js");
		$this->addCSSFile("jquery.jqplot.min.css");
	}
}
?>
