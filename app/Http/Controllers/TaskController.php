<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Message;
use Auth;
use Pusher\Pusher;
use App\Mail\TaskAdded;
use Illuminate\Support\Facades\Mail;
use App\Jobs\NotifyUser;

class TaskController extends Controller
{
    public function save_task(Request $request) {
 
        $task = new Task;
        $task->title = $request['title'];
        $task->description = $request['description'];
        $title = $request->title;

        $message = new Message;
        $message->from = Auth::user()->id;
        $id = $message->from;
        $message->message = $title;
        $message->save();
        $task->save();
        
        if($task) {
            
            // For send instant
            //  Mail::to(Auth::user()->email)->send(
            //     new TaskAdded($task)
            // );

            // send 1 min after
            // $when = now()->addMinutes(1);
            //   Mail::to(Auth::user()->email)->later(
            //    $when,
            //    new TaskAdded($task)
            //  );

            
            // after some delay
             Mail::to(Auth::user()->email)->queue(
                new TaskAdded($task)
            );

            NotifyUser::dispatch($task);
           

            return response()->json(['status' => true, 'message' => 'Task Added Successfully']);
        }
    }
}
