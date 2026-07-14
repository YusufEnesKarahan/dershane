<?php

$file = __DIR__ . '/app/Models/User.php';
$content = file_get_contents($file);

if (strpos($content, 'SoftDeletes') === false) {
    $content = str_replace("use Illuminate\Foundation\Auth\User as Authenticatable;", "use Illuminate\Foundation\Auth\User as Authenticatable;\nuse Illuminate\Database\Eloquent\SoftDeletes;", $content);
    $content = preg_replace('/use HasFactory, Notifiable;/', "use HasFactory, Notifiable, SoftDeletes;", $content);
}

if (strpos($content, 'function roles()') === false) {
    $relations = "
    public function roles() {
        return \$this->belongsToMany(Role::class);
    }
    public function teacher() {
        return \$this->hasOne(Teacher::class);
    }
";
    $content = preg_replace('/}\s*$/', "\n$relations\n}", $content);
}

file_put_contents($file, $content);

echo "User model updated successfully.\n";
