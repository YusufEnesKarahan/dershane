<?php

$modelsDir = __DIR__ . '/app/Models/';

function updateModel($className, $fillable, $relations = "") {
    global $modelsDir;
    $file = $modelsDir . $className . '.php';
    if (!file_exists($file)) return;

    $content = file_get_contents($file);
    
    // Ensure SoftDeletes trait is used
    if (strpos($content, 'SoftDeletes') === false) {
        $content = str_replace("use Illuminate\Database\Eloquent\Model;", "use Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;", $content);
        $content = preg_replace('/use HasFactory;/', "use HasFactory, SoftDeletes;", $content);
    }
    
    // Add fillable property if not present
    if (strpos($content, '$fillable') === false) {
        $fillableCode = "\n    protected \$fillable = [\n        " . implode(",\n        ", array_map(function($i) { return "'$i'"; }, $fillable)) . "\n    ];\n\n";
        $content = preg_replace('/use HasFactory, SoftDeletes;(.*?)\n/s', "use HasFactory, SoftDeletes;\n$fillableCode", $content);
    }

    // Add relations
    if ($relations !== "" && strpos($content, 'function ' . explode('()', trim(explode('public function', $relations)[1]))[0]) === false) {
        $content = preg_replace('/}\s*$/', "\n$relations\n}", $content);
    }

    file_put_contents($file, $content);
}

updateModel('Role', ['name', 'guard_name'], "
    public function users() {
        return \$this->belongsToMany(User::class);
    }
    public function permissions() {
        return \$this->belongsToMany(Permission::class);
    }
");

updateModel('Permission', ['name', 'guard_name'], "
    public function roles() {
        return \$this->belongsToMany(Role::class);
    }
");

updateModel('Branch', ['name', 'slug', 'phone', 'email', 'address', 'manager_id'], "
    public function manager() {
        return \$this->belongsTo(User::class, 'manager_id');
    }
    public function teachers() {
        return \$this->hasMany(Teacher::class);
    }
    public function courses() {
        return \$this->hasMany(Course::class);
    }
");

updateModel('Media', ['model_type', 'model_id', 'collection_name', 'name', 'file_name', 'mime_type', 'disk', 'size', 'manipulations', 'custom_properties', 'generated_conversions', 'responsive_images', 'order_column'], "
    public function model() {
        return \$this->morphTo();
    }
");

updateModel('Document', ['title', 'description', 'file_path', 'type'], "");
updateModel('Setting', ['key', 'value', 'group'], "");
updateModel('Page', ['title', 'slug', 'content', 'is_published', 'meta_title', 'meta_description'], "");
updateModel('Slider', ['title', 'image_path', 'link', 'order', 'is_active'], "");
updateModel('BlogCategory', ['name', 'slug', 'description'], "
    public function blogs() {
        return \$this->hasMany(Blog::class, 'category_id');
    }
");
updateModel('Blog', ['title', 'slug', 'content', 'category_id', 'author_id', 'image_path', 'is_published', 'published_at'], "
    public function category() {
        return \$this->belongsTo(BlogCategory::class, 'category_id');
    }
    public function author() {
        return \$this->belongsTo(User::class, 'author_id');
    }
");
updateModel('Event', ['title', 'slug', 'description', 'event_date', 'location', 'image_path'], "");
updateModel('Announcement', ['title', 'slug', 'content', 'is_active', 'published_at'], "");
updateModel('Gallery', ['title', 'description', 'image_path', 'order', 'is_active'], "");

updateModel('Teacher', ['user_id', 'branch_id', 'title', 'bio', 'specialities'], "
    public function user() {
        return \$this->belongsTo(User::class);
    }
    public function branch() {
        return \$this->belongsTo(Branch::class);
    }
");

updateModel('Course', ['name', 'slug', 'description', 'price', 'duration', 'branch_id', 'is_active'], "
    public function branch() {
        return \$this->belongsTo(Branch::class);
    }
");

updateModel('Classroom', ['name', 'branch_id', 'capacity'], "
    public function branch() {
        return \$this->belongsTo(Branch::class);
    }
");

updateModel('Registration', ['student_name', 'student_phone', 'parent_name', 'parent_phone', 'grade', 'program', 'branch_id', 'status'], "
    public function branch() {
        return \$this->belongsTo(Branch::class);
    }
");

updateModel('Lead', ['name', 'phone', 'email', 'source', 'status', 'notes'], "");
updateModel('ContactMessage', ['name', 'email', 'phone', 'subject', 'message', 'is_read'], "");

echo "Models updated successfully.\n";
