jQuery(document).ready(function ($){  
	
	if($('#search-outer.nectar').length > 0){
		
	    var acs_action = 'myprefix_autocompletesearch';  
	    $("body").on('focus','#s',function(){
	    	
	    	if(!$(this).hasClass('ui-autocomplete-input')) {
		    	$(this).autocomplete({ 
			    	delay: 50,
			    	position: {of: "#search-outer #search .container" },
			    	appendTo: $("#search-box"), 
			        source: function(req, response){  
			            $.getJSON(MyAcSearch.url+'?callback=?&action='+acs_action, req, response);  
			        },  
			        select: function(event, ui) {  
			            window.location.href=ui.item.link;  
			        },  
			        minLength: 2,  
			    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li>" )
					.append( "<a>" + item.image + "<span class='title'>" + item.label + "</span><br/><span class='desc'>" + item.post_type + "</span> </a>" )
					.appendTo( ul );
				}; 
			}

	    });
		 
	}
    
});  