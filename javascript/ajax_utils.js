/* *********************************************************************
 * Copyright 2008-2011 David Horn
 * 
 * $Id: ajax_utils.js 59 2012-09-19 01:17:33Z Dave $
 * 
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
  * 
  * 2012-09-14 - Personally, I am pretty much going to stop using
  *             this in favor of jquery ajax
  * 
* ******************************************************************* */
$(
	function () {
		var Ajax = function() {
			var self = this;
			
			this.getXmlHttpObject=function() {
				var xmlHttp=null;
				try {
					// Firefox, Opera 8.0+, Safari
					xmlHttp=new XMLHttpRequest();
				} catch (e) {
					//Internet Explorer
					try  {
						xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
					}
				}
				return xmlHttp;
			}

			this.execute = function(p_service_url,p_rpcname,p_params,p_callback) {
				var p_url='ajax_utils.php';
				var p_xrq = self.getXmlHttpObject();
				var p_response=null;
				var p_response_text;
				var p_response_type;
				var p_args;

				p_args="Service=" + p_service_url + "&" + "RPC=" + p_rpcname + "&Args=" + JSON.stringify(p_params);
				//alert(p_args);
				p_xrq.open("POST",p_url,true);
				p_xrq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				//p_xrq.setRequestHeader("Content-length", p_args.length);
				//p_xrq.setRequestHeader("Connection", "close");
				
				p_xrq.onreadystatechange=function() {
					if (p_xrq.readyState==4 || p_xrq.readyState=="complete") {
						p_response_text = p_xrq.responseText;
						p_response=JSON.parse(p_response_text);
						p_callback(p_response);
					}
				}
				
				p_xrq.send(p_args);
			}
		}
		AjaxHandler = new Ajax();
	}
	
);



