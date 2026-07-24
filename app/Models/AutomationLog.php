<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AutomationLog extends Model { protected $fillable=['job_name','status','payload','started_at','completed_at','error_message']; protected function casts(): array { return ['payload'=>'array','started_at'=>'datetime','completed_at'=>'datetime']; } }
