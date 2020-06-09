function replacebut() {

		jQuery('.btn-cart').each(function(index,element) {
			  var $thisbutton = jQuery( this );
			  onclickAtt =  jQuery(element).attr('title');

			  onclickAtt =  $thisbutton.attr('onclick');

			  
              if (onclickAtt != undefined) {
                  
	              buyLink = onclickAtt.substr('setLocation'.length + 2)
	  	          buyLink = buyLink.substr(0, buyLink.length - 2);

	              
	              if ( jQuery(this).closest('#product_addtocart_form').length == 0 ) {
		              
	            	  $thisbutton.attr('data-addcartlink' , buyLink);
	            	  $thisbutton.attr('onclick' , 'hn_addcart(this)');
	            	  
	              } else {
		              
	            	  $thisbutton.attr('onclick' , 'hn_addcart_form(this)');
	            	  
		              }

	              /** update the product type and product id */

	              $metaajaxcart  = jQuery(this).closest('.product-info').find('.magenestajaxcartmeta:first');

	              $product_id = jQuery($metaajaxcart).data('productid');

	              if ( $product_id != undefined)  $thisbutton.attr('data-productid' , $product_id); 

	              
	              $product_type = jQuery($metaajaxcart).data('productype');
	              if ( $product_type != undefined) { 
		              
		               $thisbutton.attr('data-producttype' , $product_type);

		            } else {
		               $thisbutton.attr('data-producttype' , 'simple');
			        }
		  	         
              }

  	          
			});


		 //for configurable product and grouped producct

		
	}
