<?php $count = count($task) ?>  
@if(isset($current_task))
    @for($i = 0; $i < $count; $i++)                
        <option value="{{ $task[$i]->number_task}}" @if(($task[$i]->id_task == $current_task->id_task)) selected @endif> 
            На место задания {{ $task[$i]->number_task }}: {{ $task[$i]->description }}</option>                   
    @endfor
    <option value="{{$count + 1}}">Вставить в конец</option>
@else
    <option value="1">Вставить в начало</option>                      
        @for($i = 0; $i < $count; $i++)                
            <option value="{{ $task[$i]->number_task + 1 }}"> Вставить после задания {{ $task[$i]->number_task }}: {{ $task[$i]->description }}</option>                   
        @endfor
    @if ($count != 0) <option value="{{$count + 1}}"selected>Вставить в конец</option> @endif 
@endif