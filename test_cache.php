<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$val = DB::table('cache')->where('key', 'laravel-cache-feature_flags:all')->value('value');
if ($val) {
    echo "Found cache value. Unserializing...\n";
    $obj = unserialize($val);
    var_dump($obj);
} else {
    echo "Cache key not found.\n";
    // Let's populate it
    $flags = App\Models\FeatureFlag::all();
    $serialized = serialize($flags);
    echo "Serialized:\n";
    echo substr($serialized, 0, 100) . "...\n";
    
    $obj = unserialize($serialized);
    if ($obj instanceof __PHP_Incomplete_Class) {
        echo "Incomplete class directly from serialize->unserialize!\n";
    } else {
        echo "Successfully unserialized.\n";
    }
}
