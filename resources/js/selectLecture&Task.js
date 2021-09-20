var ChangeAnswer = function(){
    $('input[name^="right_answer[]"]').each(function(){
        var value = '0';
        if($(this).is(':checked'))      
            value = '1';        
        $(this).closest('.row').find('.col-md-7').attr('data-checked', value);
    });

    $('.col-md-3').each(function() {
        $(this).remove();
    });
    
    var select_type = $("#type_task").val();
    var type;
    if (select_type == 1)
        type = "radio";        
    else if (select_type == 2)
        type = "checkbox";       
    
    if (select_type == 1 || select_type == 2){
        var count = 0;
        $('.col-md-7').each(function() {
            var checked = '';
            if($(this).attr('data-checked') == '1')
                checked = 'checked';            
            $(this).after( 
            '<div class="col-md-3">  '+              
                '<div class="form-check">'+
                    '<input class="form-check-input" type='+ type +' name="right_answer[]" id="check'+ count + '" value="'+ count + '" ' + checked +'>'+                    
                    '<label class="form-check-label" for="check'+ count++ + '">'+
                        'Верный ответ'+
                    '</label>'+
                '</div>'+            
            '</div>');
        });
    }   
}

$(document).ready(function() {
    var ChangeTopic = function(){
        var number_topic = $('#number_topic').val();
        var id = $('#data').data('id');      
        var token = $("input[name='_token']").val();
        
        $.ajax({
            url: "/admin_account/add_task/selectLecture",
            method: 'POST',
            data: {number_topic: number_topic, id: id, _token:token},
            success: function(data) {                
                $("#number_lecture").html(data.options);                
                var number_lecture = $('#number_lecture').val();
                if (number_lecture != 0){
                    $("#number_lecture").prop("disabled", false);
                    $("#number_task").prop("disabled", false);
                    $("#submit").prop("disabled", false);
                    ChangeLec();
                }
                else{
                    $("#number_task").html('');
                    $("#number_lecture").prop("disabled", true);
                    $("#number_task").prop("disabled", true);
                    $("#submit").prop("disabled", true);
                }
            }
        });
    }

    var ChangeLec = function(){
        var number_topic = $('#number_topic').val();   
        var number_lecture = $('#number_lecture').val();
        var id = $('#data').data('id');      
        var token = $("input[name='_token']").val();
      
        $.ajax({
            url: "/admin_account/add_taskselectTask",
            method: 'POST',
            data: {number_topic: number_topic, number_lecture: number_lecture, id: id, _token:token},
            success: function(data) {                
                $("#number_task").html(data.options);
            }
        });
    }

    ChangeTopic();

    ChangeAnswer();

    $("#number_topic").change(ChangeTopic);
 
    $("#number_lecture").change(ChangeLec);

    $("#type_task").change(ChangeAnswer);
    
    $('[data-toggle="tooltip"]').tooltip();     
});

$(document).on('click', '.plus', function(event) {
    event.preventDefault();            
    var field = $(this).closest('.row');
    var field_new = field.clone();   
   
    $(this)
    .toggleClass('btn-success')
    .toggleClass('plus')
    .toggleClass('btn-danger')
    .toggleClass('minus')
    .html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">'+
                    '<path d="M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z"/>'+
                '</svg>');
    $(this).attr('data-bs-original-title', 'Удалить текущее поле');
    
    field_new.find('.col-md-7').attr('data-checked', '0');
    field_new.find('input[name^="right_answer[]').prop('checked', false);
    field_new.find('input').val('');    
    
    field_new.insertAfter(field);    
    var count = 1;
    $('.input-group-text').each(function() {
        $(this).html(count++);
    });
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="tooltip"]').tooltip('hide');
    ChangeAnswer();
    
});

$(document).on('click', '.minus', function(event) {
    event.preventDefault();
    $(this).tooltip('dispose');
    
    $(this).closest('.row').remove();
    var count = 1;
    $('.input-group-text').each(function() {
        $(this).html(count++);
    });
    
    ChangeAnswer();
});