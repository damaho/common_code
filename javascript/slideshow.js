var SlideShowManager = {};
var SlideShow = {};

$(function() {
	var ss = function(m_id, m_direction, m_height) {
		var self = this;
		
		this.y_start = 0;
		this.y_finish = 0;

		this.do_switch = function(p_active, p_next) {
			p_active.addClass('last-active');
			p_active.animate({top: this.y_finish + 'px'}, 1000);
			p_next.css({top: this.y_start + 'px'})
				.addClass('active')
				.animate({top: '0px'}, 1000, function() {
					p_active.removeClass('active last-active');
				});
		}

		this.slide_switch = function() {
			var $active = $('#' + m_id + ' IMG.active');
			
			if ( $active.length == 0 ) 
				$active = $('#' + m_id + ' IMG:last');
			
			var $next =  $active.next().length ? $active.next() : $('#' + m_id + ' IMG:first');

			this.do_switch($active,$next);

		}
		
		this.init = function() {
			if (m_direction == 'up') {
				this.y_start = parseInt(m_height);
				this.y_finish = parseInt(m_height) * -1;
			} else {
				this.y_start = parseInt(m_height) * -1;
				this.y_finish = parseInt(m_height);
			}

			var $active = $('#' + m_id + ' IMG.active');
			$active.css({top:'0px'});
		}
		

		this.init();
	}
	
	var ssman = function() {
		var slideshows = {};
		
		this.addSlideShow=function(p_name, p_interval, p_direction, p_height) {
			slideshows[p_name] = new ss(p_name, p_direction, p_height );
			setInterval( "SlideShowManager.call_switch('" + p_name + "')", p_interval);
		}
		
		this.call_switch = function(p_name) {
			slideshows[p_name].slide_switch();
		}
	}

	SlideShowManager = new ssman();
});

