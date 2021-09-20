$(document).ready(function() {
    $("a[href='" + window.location.href + "']").addClass('active');

    var cur = $('#list a.active').closest('li');
    var prev = cur.prevAll('li');
    var next = cur.nextAll('li');    
    
    if(typeof prev.children('a').attr('href') !== "undefined"){        
        $('#back').prop('href', prev.children('a').attr('href'));
        $('#back').css('display','block'); 
    }   

    if(typeof next.children('a').attr('href') !== "undefined") {       
        $('#next').prop('href', next.children('a').attr('href')); 
        $('#next').css("display","block");
    }      
});