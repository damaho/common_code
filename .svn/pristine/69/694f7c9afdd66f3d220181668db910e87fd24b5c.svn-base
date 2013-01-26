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
 * $Id$
 * 
* ****************************************************************** */
$(function() {
	function Tabber(m_tabber_object) {
		var m_content_tab;
		var self = this;
		
		this.tabs = [];
		
		this.makeActiveTab = function(obj) {
			m_tabber_object.find('.tabbertab').removeClass('tabbertab_active');
			m_tabber_object.find('.tabbertab').addClass('tabbertab_inactive');
			obj.removeClass('tabbertab_inactive');
			obj.addClass('tabbertab_active');
		}
		
		this.resetTabContent = function() {
			var content;
			var l = this.tabs.length;
			for (var i=0; i < l; i++) {
				if (this.tabs[i].active_tab) {
					content = m_content_tab.find('div:first');
					content.addClass('nodisplay');
					this.tabs[i].append(content);
				}
				this.tabs[i].active_tab = false;
			}
		}
		
		this.makeActive = function(idx) {
			var obj = this.tabs[idx];

			self.resetTabContent();

			this.tabs[idx].active_tab = true;
			this.makeActiveTab(obj);

			var active_content = obj.find('div:first');
			
			//m_content_tab.fadeOut(100);
			//m_content_tab.empty();
			m_content_tab.append(active_content);
			active_content.removeClass('nodisplay');
			//m_content_tab.fadeIn(100);
		}
		
		this.init = function() {
			m_tabber_object.append($('<div></div>')
						   .attr('id','tabcontent')
			);
			m_content_tab = m_tabber_object.find('#tabcontent');
			m_tabber_object.find('.tabbertab').each(
				function (index) {
					self.tabs[index] = $(this);
					self.tabs[index].active_tab = false;
					$(this).find('div:first').addClass('nodisplay');
					$(this).bind('mouseover',function() { $(this).addClass('hovertab'); });
					$(this).bind('mouseout',function() { $(this).removeClass('hovertab'); });
					$(this).bind('click',function() { self.makeActive(index); });
					if (index == 0)
						self.makeActive(index);
				}
			);
		}
		
		this.init();
	}
	
	function TabberManager() {
		var self = this;
		
		this.tabbers = {};
		
		this.get_tabbers = function() {
			var prefix = 'tabber__';
			$('.tabber').each(function(index) {
					self.tabbers[prefix + index] = new Tabber($(this));
				}
			);
			
		}
		
		this.init = function() {
			self.get_tabbers();
		}
		this.init();
	}
	
	Tabber = new TabberManager();
});
