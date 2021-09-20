@if (count($lecture) != 0)
    @for($i = 0; $i < count($lecture); $i++) 
        <option value="{{ $lecture[$i]->number_lecture}}" @if((isset($current_lecture) && $lecture[$i]->id_lecture == $current_lecture->id_lecture)) selected @endif>
            Лекция {{ $lecture[$i]->number_lecture }}: {{ $lecture[$i]->title }}</option>
    @endfor
@else
    <option value="0">У данной темы нет лекций</option>
@endif