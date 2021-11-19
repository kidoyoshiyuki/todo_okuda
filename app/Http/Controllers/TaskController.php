<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
 
use App\Models\Task;

use App\Repositories\TaskRepository;    //Repositoryを呼び出す
 
class TaskController extends Controller
{
    /** 
     * タスクリポジトリ
     * 
     * @var TaskRepository
     */
    protected $tasks;

    /** 
     * コンストラクタ(自動で呼び出される初期化処理用のメソット)
     * 
     * @return void
     */
    //認証機能をTaskControllerで有効にするため
    public function __construct(TaskRepository $tasks)  //コンストラクタでタスクリポジトリから受け取るように引数を設定
    {
        $this->middleware('auth');

        $this->tasks = $tasks;
    }


    /**
     * タスク一覧
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //$tasks = Task::orderBy('created_at', 'asc')->get();
        //$tasks = $request->user()->tasks()->get();  requestがuserメソットにて認証済みのuserを取得、そのuserが保持するtask一覧を取得
        return view('tasks.index', [    //tasksのindexを使用するという意味
            'tasks' => $this->tasks->forUser($request->user()),
        ]);
    }
    /**
     * タスク登録
     * 
     * @param Request $request
     * @return Response
     */

     public function store (Request $request)
     {
         $this->validate($request, [
             'name' => 'required|max:255',  //名前が必須で255文字できるという意味
         ]);

         //タスク作成
         //Task::create([
         // 'user_id' => 0,
         // 'name' => $request->name
         //]);

         $request->user()->tasks()->create([
             'name' => $request->name,
         ]);

         return redirect('/tasks');
     }

     /*
      *タスク削除
      *
      * @param Request $request
      * @parm Task $task
      * @return Response
      *
      */
      public function destroy(Request $request, Task $task)
      {
          $this->authorize('destroy', $task);

          $task->delete();
          return redirect('/tasks');
      }
}
    
