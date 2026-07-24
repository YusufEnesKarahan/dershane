<?php
namespace App\Http\Controllers\Admin;
use App\Core\Repositories\Interfaces\{AutomationLogRepositoryInterface,JobHistoryRepositoryInterface};
use App\Http\Controllers\Controller;
use App\Models\JobHistory;
use Illuminate\Support\Facades\DB;
class SystemJobController extends Controller { public function __construct(private readonly JobHistoryRepositoryInterface $histories, private readonly AutomationLogRepositoryInterface $automationLogs) {} public function dashboard() { $this->authorize('manage', JobHistory::class); return view('admin.system.jobs.dashboard',['pending'=>DB::table('jobs')->count(),'failed'=>DB::table('failed_jobs')->count(),'completed'=>JobHistory::where('status','completed')->count(),'recent'=>$this->histories->paginate(10)]); } public function failed() { $this->authorize('manage', JobHistory::class); return view('admin.system.jobs.failed',['jobs'=>DB::table('failed_jobs')->latest('failed_at')->paginate(20)]); } public function history() { $this->authorize('manage', JobHistory::class); return view('admin.system.jobs.history',['jobs'=>$this->histories->paginate()]); } public function automation() { $this->authorize('manage', JobHistory::class); return view('admin.system.jobs.automation',['logs'=>$this->automationLogs->paginate()]); } }
