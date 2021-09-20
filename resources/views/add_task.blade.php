@extends('layout')

@if(isset($current_task)) 
    @section('title')Редактирование задания {{$current_task->number_task}}. {{$current_task->description}}@endsection
@else
    @section('title')Добавление задания@endsection
@endif

@section('head')
@include('inc.head_summernote')
<link rel="stylesheet" href="{{ asset('css/add_content.css') }}">
<link rel="stylesheet" href="{{ asset('css/add_task.css') }}">
<script src="{{ asset('js/selectLecture&Task.js') }}"></script>
@endsection

@section('body')
@if(isset($current_task))
    <div class="bg-light border rounded-3 shadow-sm p-3 pt-4">
        <h5 class="d-flex justify-content-center">Редактирование задания {{$current_task->number_task}}. {{$current_task->description}}</h5>
    </div>
    <form class="form" method="post" action="{{route('update_task_res', $current_task->id_task)}}">
@else
    <div class="bg-light border rounded-3 shadow-sm p-3 pt-4">
        <h5 class="d-flex justify-content-center">Добавление нового задания</h5>
    </div>
    <form class="form" method="post" action="{{route('add_task_res')}}">
@endif
    @csrf
    <div class="form-group">
        <label for="number_topic">К какой теме относится задание</label>
        <select class="form-select shadow-sm" name="number_topic" id="number_topic">  
            @if(isset($current_task)) 
            <?php $current_lecture = App\Lecture::find($current_task->id_lecture);
            $current_topic = App\Topic::where('id_topic', $current_lecture->id_topic)->value('number_topic');?> 
            @endif       
            @for($i = 0; $i < count($topic); $i++)
            <option value="{{ $topic[$i]->number_topic}}" @if(isset($current_task) && $topic[$i]->number_topic == $current_topic) selected @endif>
                Тема {{ $topic[$i]->number_topic }}: {{ $topic[$i]->title }} 
                </option>
            @endfor 
        </select>
    </div>    
    <div class="form-group">
        <label for="number_lecture">К какой лекции относится задание</label>
        <select class="form-select shadow-sm" name="number_lecture" id="number_lecture"></select>
    </div>
    <div class="form-group" >
        <label for="number_task">Место вставки задания</label>
        <select class="form-select shadow-sm" name="number_task" id="number_task" required></select>
    </div> 
    <div class="form-group" >
        <label for="type_task">Тип задания</label>
        <select class="form-select shadow-sm" name="type_task" id="type_task" >           
            @for($i = 0; $i < count($type_task); $i++)
            <option value="{{ $i + 1 }}" @if(isset($current_task) && $type_task[$i]->id_type_task == $current_task->id_type_task) selected @endif>
                Тип {{ $i + 1 }}: {{ $type_task[$i]->type_task }} </option>
            @endfor     
        </select>
    </div>
    <div class="form-group">
        <label for="description">Описание задания</label>
        <input type="text" class="form-control shadow-sm" name="description" id="description" value ="@if(isset($current_task)){{$current_task->description}}@endif" required>
    </div>   
    <div class="form-group">
        <label for="content">Текст задания (необязательно)</label>
        <textarea class="form-control shadow-sm" name="content" id="content">@if(isset($current_task)){{$current_task->text_task}}@endif</textarea>
    </div> 
    <div class="form-group">
        <label for="answer[]">Ответы</label>
        @if(isset($current_task))
            @for($i = 0; $i < count($answer); $i++)
            <div class="row">
                <div class="col-md-7" @if($answer[$i]->isRight == 1) data-checked="1" @else data-checked="0" @endif>  
                    <div class="input-group shadow-sm">                
                        <div class="input-group-text ">{{$i + 1}}</div>
                        <input type="text" class="form-control answer" name="answer[]" value ="{{$answer[$i]->text_answer}}"required>
                        @if($i == (count($answer) - 1))
                        <span class="btn btn-success plus pull-right" data-toggle="tooltip" title="Добавить новое поле для ответа">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                            </svg>
                        </span>     
                        @else
                        <span class="btn btn-danger minus pull-right" data-toggle="tooltip"  title="Удалить текущее поле">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
                                <path d="M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z"/>
                            </svg>
                        </span> 
                        @endif         
                    </div>
                </div>
            </div>
            @endfor
        @else        
            <div class="row">
                <div class="col-md-7" data-checked="0">  
                    <div class="input-group shadow-sm">                
                        <div class="input-group-text ">1</div>
                        <input type="text" class="form-control answer" name="answer[]" required>
                        <span class="btn btn-danger minus pull-right" data-toggle="tooltip"  title="Удалить текущее поле">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
                                <path d="M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z"/>
                            </svg>
                        </span>          
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7" data-checked="0">
                    <div class="input-group shadow-sm">
                        <div class="input-group-text">2</div>
                        <input type="text" class="form-control answer" name="answer[]" required>
                        <span class="btn btn-success plus pull-right" data-toggle="tooltip" title="Добавить новое поле для ответа">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                            </svg>
                        </span>            
                    </div>
                </div>
            </div> 
        @endif    
    </div>
    <div class="d-grid d-lg-flex justify-content-lg-center">
        <button type="submit" class="btn btn-outline-success shadow-sm" id="submit">@if(isset($current_lecture))Обновить задание@elseДобавить задание@endif</button>        
    </div>
</form>
@if(isset($current_task)) 
    <div id="data" data-id="{{$current_task->id_task}}"></div>
@endif
@endsection