@extends('layout')

@section('title')Задание {{$cur_task->number_task}}: {{$cur_task->description}} @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('css/vs.css') }}">
<link rel="stylesheet" href="{{ asset('css/sidebars.css') }}">
<link rel="stylesheet" href="{{ asset('css/task.css') }}">

<script src="{{ asset('js/highlight.pack.js') }}"></script>
<script src="{{ asset('js/checkAnswer.js') }}"></script>
<script src="{{ asset('js/moving_content.js') }}"></script>
@endsection

@section('body')
<div id="text">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <h4>Задание {{$cur_task->number_task}}. {{$cur_task->description}}</h4>
            </div>
            <hr>
            <div class="lead" id="lead">{!!$cur_task->text_task!!}</div>
            @if($cur_task->text_task != null)<hr>@endif

            <div class="border border-2 rounded-3 shadow-sm" id="border">
                @if($cur_task->id_type_task == 1)
                <h6> Варианты ответа: </h6>
                @elseif($cur_task->id_type_task == 2)
                <h6> Варианты ответов: </h6>
                @else
                <h6> Поля для ввода ответов: </h6>
                @endif

                @for($i = 0; $i < count($answer); $i++) 
                    @if($cur_task->id_type_task == 1 || $cur_task->id_type_task == 2)
                    <div class="form-check el">
                        <input class="form-check-input" 
                        @if($cur_task->id_type_task == 1) 
                        type="radio" 
                        @else 
                        type="checkbox" 
                        @endif 
                        name="answer[]" id="check{{$answer[$i]->id_answer}}" value="{{$answer[$i]->id_answer}}">

                        <label class="form-check-label" for="check{{$answer[$i]->id_answer}}">
                            {{$answer[$i]->text_answer}}
                        </label>
                    </div>
                    @else
                    <div class="input-group el">
                        <div class="input-group-text">{{$i + 1}}</div>
                        <input type="text" class="form-control" name="answer[]" id="check{{$answer[$i]->id_answer}}">
                    </div>
                    @endif
                @endfor
            </div>

            <div class="row" id="line_buttons">
                <div class="col d-grid d-md-flex justify-content-md-start">
                    <a class="btn btn-outline-secondary" id="back"><- Назад</a>
                </div>
                <div class="col d-grid d-md-flex justify-content-md-end" id="button">
                    <a class="btn btn-outline-success" id="check">Проверить</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @include('inc.list_topic')
        </div>
    </div>
</div>
<div id="data" data-id="{{$cur_task->id_task}}" data-type="{{$cur_task->id_type_task}}"></div>
<script>
    hljs.highlightAll();
</script>
@endsection