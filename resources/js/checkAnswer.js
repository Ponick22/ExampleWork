$(document).ready(function() {
    $("#check").click(function(){        
        var id = $('#data').data('id');
        var type = $('#data').data('type');
        var page = $('#data').data('page'); 
        var answer;
        
        if (type == 1 || type == 2)
            answer = $('input[name^="answer[]"]:checked').map(function(){return $(this).val();}).get();
        else 
            answer = $('input[name^="answer[]"]').map(function(){return $(this).val();}).get();
        var token = $("input[name='_token']").val();  

        $.ajax({
            url: "/check_answer",
            method: 'POST',
            data: {id: id, type: type, answer: answer, page:page, _token:token},
            success: function(data){
                $('#alert').remove();
                var content;  
                if (data.mode == 'success'){
                    content =  
                    '<div class="alert alert-success d-flex" id="alert">' +
                        '<div class="d-flex align-items-center">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-check" viewBox="0 0 15 15">' +
                            '<path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>' +
                            '</svg>' +
                        '</div>' +
                        '<div class="d-flex align-items-center" id="text_mes">' +
                            'Дан верный ответ!' +
                        '</div>' +
                    '</div>';
                    $('#check').remove();                    
                                       
                    var cur = $('#list a.active').closest('li');    
                    var next = cur.nextAll('li');      
                    if(typeof next.children('a').attr('href') !== "undefined") {                        
                        $('#button').html('<a class="btn btn-outline-secondary" id="next" href ="' + next.children('a').attr('href') +'">Далее -></a>');
                        $('#next').css("display","block");                        
                    }
                    else if(page == 'random'){
                        $('#button').html('<a class="btn btn-outline-secondary" id="next" href ="/tasks/random_task">Далее -></a>');
                        $('#next').css("display","block");   
                    }     
                }
                else if (data.mode == 'error')
                    content =  
                    '<div class="alert alert-danger d-flex" id="alert">' +
                        '<div class="d-flex align-items-center">' +                        
                            '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x" viewBox="0 0 15 15">'+
                            '<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>'+
                            '</svg>'+              
                        '</div>' +
                        '<div class="d-flex align-items-center" id="text_mes">' +
                            'Неверный ответ! Повторите попытку!' +
                        '</div>' +
                    '</div>';
                
                $('#line_buttons').after(content);

                $('.form-check-input, .form-control').each(function() {
                    $(this).removeClass('is-valid is-invalid');
                });
                if(data.right.length) 
                    for(var i = 0; i < data.right.length;i++)                        
                        $('#check'+data.right[i]).addClass('is-valid');
                                    
                if(data.not_right.length) 
                    for(var i = 0; i < data.not_right.length;i++)                                    
                        $('#check'+data.not_right[i]).addClass('is-invalid');                                   
            }            
        });
    });    
});