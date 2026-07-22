<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentComment extends Model
{
    protected $fillable = ['submission_id', 'user_id', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
