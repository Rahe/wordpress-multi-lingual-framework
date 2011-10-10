jQuery(function ($) {
    $(document).ready(function() {

        /* used in plugin settings page */        
        $('.mlf_options').slideUp();

        $('.mlf_section h3').click(function(){      
            if($(this).parent().next('.mlf_options').css('display')=='none')
                {   $(this).removeClass('inactive');
                    $(this).addClass('active');
                    $(this).children('img').removeClass('inactive');
                    $(this).children('img').addClass('active');

                }
            else
                {   $(this).removeClass('active');
                    $(this).addClass('inactive');      
                    $(this).children('img').removeClass('active');         
                    $(this).children('img').addClass('inactive');
                }

            $(this).parent().next('.mlf_options').slideToggle('slow');  
        });
 
        /* used to present metabox of other versions of post */

        $(".translation_content").hide(); //Hide all content        
        $("ul.translation_tabs li:first").addClass("active").show(); //Activate first tab
        $(".translation_content:first").show(); //Show first tab content    

        $("ul.translation_tabs li").click(function() {

            $("ul.translation_tabs li").removeClass("active"); //Remove any "active" class
            $(this).addClass("active"); //Add "active" class to selected tab
            $(".translation_content").hide(); //Hide all tab content

            var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
            $(activeTab).fadeIn(); //Fade in the active ID content
            return false;
        });
       
    });
});
