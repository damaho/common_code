<?php
/* *********************************************************************
 * Copyright (C) 2007-2012 Brew Net
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
 * $Id: ajax_image.php 74 2012-10-20 22:29:27Z Dave $
 * 
* ****************************************************************** */
require_once ('AjaxImage.php');

if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
	$foo = new AjaxImage();
	$foo->execute();
	$foo->printResult();
}
?>
