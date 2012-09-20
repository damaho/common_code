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
* ****************************************************************** */
var PageLayout = {};

$(function() {
		var General = function() {
			this.method = {};
			this.attribute = {};
			this.objects = {};
			
			this.add_meth = function (p_method_name, p_method) {
				this.method[p_method_name] = p_method
			}

			this.add_attrib =  function (p_name, p_value) {
				this.attribute[p_name] = p_value;
			}
			
			this.add_object = function (p_name, p_instance) {
				this.objects[p_name] = p_instance;
				if (p_instance.init != undefined)
					p_instance.init();
			}
			
			
		}
		
		PageLayout = new General();
	}
)
