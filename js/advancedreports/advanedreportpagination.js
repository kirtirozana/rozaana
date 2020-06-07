
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Advancedreports
 * @version     0.2.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */

(function($) {
	$.extend({
		advancedtablesorterPager: new function() {
			
			
			
			function updatePageDisplay(c) {			
                        $(c.goTo,c.container).val((c.page+1));	                        
                        $(c.noPage,c.container).text(c.seperator+' '+c.totalPages);                                                                                    
                        }                           
               			
			function setPageSize(table,size) {
				var c = table.config;
				c.size = size;
				c.totalPages = Math.ceil(c.totalRows / c.size);
				c.pagerPositionSet = false;
				moveToPage(table);
				fixPosition(table);
			}                        
                        
                        	function setPageGoto(table,goto) {                                  
				var c = table.config;
				c.goto = goto;                              
				goToPage(table);                              
			}
                        
                        
			
			function fixPosition(table) {
				var c = table.config;
				if(!c.pagerPositionSet && c.positionFixed) {
					var c = table.config, o = $(table);
					if(o.offset) {
						c.container.css({
						});
					}
					c.pagerPositionSet = true;
				}
			}
			
			function moveToFirstPage(table) {
				var c = table.config;
				c.page = 0;
				moveToPage(table);
			}
			
			function moveToLastPage(table) {
				var c = table.config;
				c.page = (c.totalPages-1);
				moveToPage(table);
			}
			
			function moveToNextPage(table) {                          
                           	var c = table.config;				
                                c.page++;
				if(c.page >= (c.totalPages-1)) {
					c.page = (c.totalPages-1);
				}
				moveToPage(table);
			}
			
			function moveToPrevPage(table) {
				var c = table.config;
				c.page--;
				if(c.page <= 0) {
					c.page = 0;
				}
				moveToPage(table);
			}
						
			
			function moveToPage(table) {
				var c = table.config;
				if(c.page < 0 || c.page > (c.totalPages-1)) {
					c.page = 0;                            
				}
				
				renderTable(table,c.rowsCopy);
			}                    
                        
                                function goToPage(table) {
				var c = table.config;
				if(c.goto > 0 || c.goto < c.totalPages) {
			        c.page = (c.goto-1);                                  
				}
                                
				renderTable(table,c.rowsCopy);
			        }
				
			
			function renderTable(table,rows) {
				
				var c = table.config;
				var l = rows.length;
				var s = (c.page * c.size);
				var e = (s + c.size);
				if(e > rows.length ) {
					e = rows.length;
				}                              
                        
				
				var tableBody = $(table.tBodies[0]);
				
				// clear the table body
				
				$.tablesorter.clearTableBody(table);
				
				for(var i = s; i < e; i++) {				
					
					
					var o = rows[i];
					var l = o.length;
					for(var j=0; j < l; j++) {
						
						tableBody[0].appendChild(o[j]);

					}
				}
				
				fixPosition(table,tableBody);
				
				$(table).trigger("applyWidgets");
				
				if( c.page >= c.totalPages ) {
        			moveToLastPage(table);
				}
				
			 	updatePageDisplay(c);
			}
			
			this.appender = function(table,rows) {
				
				var c = table.config;
				
				c.rowsCopy = rows;
				c.totalRows = rows.length;
				c.totalPages = Math.ceil(c.totalRows / c.size);
				
				renderTable(table,rows);
			};
			
			this.defaults = {
				size: 5,
				offset: 0,
				page: 0,
				totalRows: 0,
				totalPages: 0,
				container: null,
				cssNext: '.next',
				cssPrev: '.prev',
				cssFirst: '.first',
				cssLast: '.last',			
				cssPageSize: '.pagesize',
				seperator: "/",
				positionFixed: true,
                                goTo: '.goto',
                                noPage: '.nopage',                               
				appender: this.appender
			};
			
			this.construct = function(settings) {
				
				return this.each(function() {	
					
					config = $.extend(this.config, $.advancedtablesorterPager.defaults, settings);
					
					var table = this, pager = config.container;
				
					$(this).trigger("appendCache");
					
					config.size = parseInt($(".pagesize",pager).val());
                                        config.goto = parseInt($(".goto",pager).val());
					
					$(config.cssFirst,pager).click(function() {						
                                                moveToFirstPage(table);
						return false;
					});
					$(config.cssNext,pager).click(function() {
						moveToNextPage(table);
						return false;
					});
					$(config.cssPrev,pager).click(function() {
						moveToPrevPage(table);
						return false;
					});
					$(config.cssLast,pager).click(function() {
						moveToLastPage(table);
						return false;
					});
					$(config.cssPageSize,pager).change(function() {
                                                $(this).blur();
                                                $(config.goTo).val(1);
						setPageSize(table,parseInt($(this).val()));
						return false;
					});
                                           $(config.goTo,pager).change(function() {
					
                                            
                                             var goPage=$(this).val();
                                            
                                             if(goPage <= 0 || goPage == '')    
                                             {
                                              setPageGoto(table,1);
                                             }
                                             else
                                             {
                                             setPageGoto(table,parseInt($(this).val()));                                          
                                             }    
                                                                             
                                             return false;
					});
				});
			};
			
		}
	});
	
	$.fn.extend({
        advancedtablesorterPager: $.advancedtablesorterPager.construct
	});
	
})(jQuery);				