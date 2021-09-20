<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Topic;
use App\Lecture;
use App\TypeTask;
use App\Task;
use App\Answer;
use App\Progress_task;

class TaskController extends Controller
{
    public function form_addTask(){
        $topic = Topic::orderBy('number_topic')->get();
        $type = TypeTask::get();
        return view('add_task', ['topic' => $topic, 'type_task' => $type]);
    }

    public function form_updateTask($id_task){ 
        $topic = Topic::orderBy('number_topic')->get();
        $type = TypeTask::get(); 
        $answer = Answer::where('id_task', $id_task)->get();     
        return view('add_task', ['topic' => $topic, 'type_task' => $type, 'answer' => $answer, 'current_task'=> Task::find($id_task)]);
    }

    public function selectLecture(Request $req){            
        if($req->ajax()){
            $id_topic = Topic::where('number_topic', $req->number_topic)->pluck('id_topic');
            $lec = Lecture::where('id_topic', $id_topic)->orderBy('number_lecture')->get();
            if(isset($req->id)){
                $cur_lec = Task::where('id_task', $req->id)->value('id_lecture');
                $data = view('select.selectLecture2', ['lecture' => $lec, 'current_lecture'=> Lecture::find($cur_lec)])->render();
            }
            else
                $data = view('select.selectLecture2', ['lecture' => $lec])->render();            
            return response()->json(['options'=>$data]);
        }        
    }

    public function selectTask(Request $req){                   
        if($req->ajax()){
            $id_topic = Topic::where('number_topic', $req->number_topic)->pluck('id_topic');
            $id_lecture = Lecture::where('id_topic', $id_topic)->where('number_lecture', $req->number_lecture)->pluck('id_lecture');
            $task = Task::where('id_lecture', $id_lecture)->orderBy('number_task')->get();
            if(isset($req->id))
                $data = view('select.selectTask', ['task' => $task, 'current_task'=> Task::find($req->id)])->render();
            else
                $data = view('select.selectTask', ['task' => $task])->render();           
            return response()->json(['options'=>$data]);
        }        
    }

    public function addTask(Request $req){        
        $task = new Task();
        $id_topic = Topic::where('number_topic', $req->input('number_topic'))->value('id_topic');  
        $task->id_lecture = Lecture::where('id_topic', $id_topic)->where('number_lecture', $req->input('number_lecture'))->value('id_lecture');  
        $task->id_type_task = $req->input('type_task');
        $task->number_task = $req->input('number_task');
        $task->description = $req->input('description');
        $task->text_task = $req->input('content');

        Task::where('id_lecture', $task->id_lecture)->where('number_task', '>=', $task->number_task)->increment('number_task');      
        $task->save();

        $j = 0; 
        for($i = 0; $i < count($req->input('answer.*')); $i++){
            $answer = new Answer();
            $answer->id_task = $task->id_task;   
            $answer->text_answer = $req->input('answer')[$i];
            if ($task->id_type_task == 1 || $task->id_type_task == 2)
                if(count($req->input('right_answer.*')) > $j && $i == $req->input('right_answer')[$j]){
                    $answer->isRight = 1;
                    $j++; 
                }            
            $answer->save();            
        }

        return redirect()->route('personal_account_admin')->with('success', 'Задание успешно добавлено в тему ' . $req->input('number_topic') . ' лекцию ' . $req->input('number_lecture'). '!');
    }

    public function updateTask($id_task, Request $req){
        $task = Task::find($id_task);
        $id_topic = Topic::where('number_topic', $req->input('number_topic'))->value('id_topic');  

        $current_lecture = $task->id_lecture;
        $new_lecture = Lecture::where('id_topic', $id_topic)->where('number_lecture', $req->input('number_lecture'))->value('id_lecture');
        
        $current_number = $task->number_task;
        $new_number = $req->input('number_task');

        $task->id_lecture = $new_lecture; 
        $task->id_type_task = $req->input('type_task');       
        $task->number_task = $req->input('number_task');
        $task->description = $req->input('description');
        $task->text_task = $req->input('content');

        Task::where('id_lecture', $new_lecture)->where('number_task', '>=', $new_number)->increment('number_task');        
        $task->save(); 
        Task::where('id_lecture', $current_lecture)->where('number_task', '>', $current_number)->decrement('number_task');

        $current_answer = Answer::where('id_task', $id_task)->get();
        $count_current = count($current_answer);
        $count_new = count($req->input('answer.*'));
        $j = 0; 
        for($i = 0; $i < max($count_current, $count_new); $i++){
            if($i >= $count_current){
                $answer = new Answer();
                $answer->id_task = $task->id_task;   
                $answer->text_answer = $req->input('answer')[$i];
                if ($task->id_type_task == 1 || $task->id_type_task == 2)
                    if(count($req->input('right_answer.*')) > $j && $i == $req->input('right_answer')[$j]){
                        $answer->isRight = 1;
                        $j++; 
                    }            
                $answer->save();            
            }
            else if($i >= $count_new){
                $current_answer[$i]->delete();
            }
            else{
                $current_answer[$i]->text_answer = $req->input('answer')[$i];
                if ($task->id_type_task == 1 || $task->id_type_task == 2)
                    if(count($req->input('right_answer.*')) > $j && $i == $req->input('right_answer')[$j]){
                        $current_answer[$i]->isRight = 1;
                        $j++; 
                    }
                    else
                        $current_answer[$i]->isRight = 0;            
                $current_answer[$i]->save(); 
            }           
        }

        return redirect()->route('personal_account_admin')->with('success', 'Задание "'.$task->description. '" успешно обновлено!');
    }

    public function deleteTask($id_task){
        $task = Task::find($id_task);        
        $task->delete();
        Task::where('id_lecture', $task->id_lecture)->where('number_task', '>', $task->number_task)->decrement('number_task');       
        return back()->with('success', 'Задание "'.$task->description. '" успешно удалено!');        
    }

    public function getTask($id_topic, $id_lecture, $id_task){
        $type = Task::find($id_task);  
        $answer;      
        if ($type->id_type_task == '1' || $type->id_type_task == '2')
            $answer = Answer::where('id_task', $id_task)->inRandomOrder()->get();            
        else
            $answer = Answer::where('id_task', $id_task)->get();

        return view('task', ['cur_topic' => Topic::find($id_topic), 'cur_task' => Task::find($id_task), 'answer' => $answer]);
    }

    
    public function randomTask(){ 
        $task = Task::orderByRaw("RAND()")->first();
        $id_task = session('id_task');
        if($id_task !== null){            
            while($task->id_task === $id_task) 
                $task = Task::orderByRaw("RAND()")->first();
        }        
        
        $answer;      
        if ($task->id_type_task == '1' || $task->id_type_task == '2')
            $answer = Answer::where('id_task', $task->id_task)->inRandomOrder()->get();            
        else
            $answer = Answer::where('id_task', $task->id_task)->get();

        return view('random_task', ['cur_task' => $task, 'answer' => $answer]);       
    }

    public function checkAnswer(Request $req){ 
        if($req->ajax()){
            $type = $req->type;
            $current_answer = $req->answer;

            $right = array();
            $not_right = array();
            $flag = true;
            if($type === '1' || $type === '2'){
                $true_answer = Answer::where('id_task', $req->id)->where('isRight', 1)->pluck('id_answer')->toArray(); 

                if(count($current_answer) !== (count($true_answer)))
                    $flag = false;
                
                for($i = 0; $i < count($current_answer); $i++){                    
                    if(array_search($current_answer[$i], $true_answer) !== false)
                        array_push($right, $current_answer[$i]);                    
                    else{
                        $flag = false;
                        array_push($not_right, $current_answer[$i]);   
                    }
                }                
            }
            else{
                $true_answer = Answer::where('id_task', $req->id)->get();
                for($i = 0; $i < count($true_answer); $i++){
                    if($true_answer[$i]->text_answer === $current_answer[$i])
                        array_push($right, $true_answer[$i]->id_answer);
                    else{
                        $flag = false;
                        array_push($not_right, $true_answer[$i]->id_answer);   
                    }
                }
            }
            
            if($flag){
                $mode = 'success';                
                if($req->page !== 'random'){
                    if(Auth::user()->id_type_user == 1)
                        if (!Progress_task::where('id_task', $req->id)->where('id_user', Auth::user()->id_user)->exists()) {           
                            $progress = new Progress_task();
                            $progress->id_task = $req->id;
                            $progress->id_user = Auth::user()->id_user;
                            $progress->save(); 
                        }
                } 
            }               
            else
                $mode = 'error';
            return response()->json(['mode' => $mode, 'right' => $right, 'not_right' => $not_right, 'd1' => $true_answer, 'd2' => $current_answer]);            
        }               
    }
}