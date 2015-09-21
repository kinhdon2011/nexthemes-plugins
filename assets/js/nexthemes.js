/*!
 * NexThemes Plugin v1.0.0
 * Copyright 2015 Nexthemes
 */

(function($){
	"use strict";
	
	NexThemes.objs = {
		portfolio: {
			elements: $('.nth-portfolios-wrapper'),
			init: function(){
				this.elements.each(function(){
					var $this = $(this);
					
					var $gid = $this.find('.nth-portfolio-content').isotope({
						itemSelector: '.nth-portfolio-item',
						masonry: {
							//columnWidth: 100,
						}
					});
					
					$this.find('ul.nth-portfolio-filters li').on('click', function(){
						$(this).parent().find('li.active').removeClass( 'active' );
						$(this).addClass( 'active' );
						var filter_class = $(this).find('a').data('filter');
						$gid.isotope({ filter: filter_class });
					});
				});
			}
		},
		gridlistToggle: {
			elements: $('.nth-shop-meta-controls .gridlist-toggle'),
			init: function(){
				$('#grid').on('click', function(){
					$(this).addClass('active');
					$('#list').removeClass('active');
					$('#table').removeClass('active');
					$.cookie('gridcookie','grid', { path: '/' });
					$('.products').fadeOut(300, function() {
						$(this).addClass('grid').removeClass('table').removeClass('list').fadeIn(300);
					});
					return false;
				})
				
				$('#list').on('click', function() {
					$(this).addClass('active');
					$('#grid').removeClass('active');
					$('#table').removeClass('active');
					$.cookie('gridcookie','list', { path: '/' });
					$('.products').fadeOut(300, function() {
						$(this).removeClass('grid').removeClass('table').addClass('list').fadeIn(300);
					});
					return false;
				});
				
				$('#table').on('click', function() {
					$(this).addClass('active');
					$('#grid').removeClass('active');
					$('#list').removeClass('active');
					$.cookie('gridcookie','table', { path: '/' });
					$('.products').fadeOut(300, function() {
						$(this).removeClass('grid').removeClass('list').addClass('table').fadeIn(300);
					});
					return false;
				});

				if ($.cookie('gridcookie')) {
					$('.products').removeClass('table').removeClass('list').removeClass('grid').addClass($.cookie('gridcookie'));
				}

				if ($.cookie('gridcookie') == 'grid') {
					$('.gridlist-toggle #grid').addClass('active');
					$('.gridlist-toggle #list').removeClass('active');
					$('.gridlist-toggle #table').removeClass('active');
				}

				if ($.cookie('gridcookie') == 'list') {
					$('.gridlist-toggle #list').addClass('active');
					$('.gridlist-toggle #grid').removeClass('active');
					$('.gridlist-toggle #table').removeClass('active');
				}
				if ($.cookie('gridcookie') == 'table') {
					$('.gridlist-toggle #table').addClass('active');
					$('.gridlist-toggle #grid').removeClass('active');
					$('.gridlist-toggle #list').removeClass('active');
				}

				$('.gridlist-toggle a').click(function(event) {
					event.preventDefault();
				});
			}
		}
	}
	
	$(document).ready(function(){
		
		$("a[data-rel^='prettyPhoto']").prettyPhoto({
			social_tools : false,
			theme: 'pp_default',
		});
		
		$.each( NexThemes.objs, function( key, value ){
			if(value.elements.length) {
				value.init();
			}
		} );
		
	});
	
})(jQuery);