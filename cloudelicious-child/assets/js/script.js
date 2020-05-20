jQuery(document).ready(function(){
    jQuery(".searchform input[type='button']").click(function(){    
        jQuery(this).parent().parent('#searchform').submit(); 
    });
  
   jQuery( ".search-no-results .site-main .entry-content #searchform" ).wrap( "<div class='widget'></div>" );
});