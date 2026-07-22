<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentScore extends Model
{
    protected $fillable = ['submission_id', 'evaluator_id', 'score', 'max_score', 'feedback'];

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
