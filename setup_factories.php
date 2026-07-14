<?php

$factoriesDir = __DIR__ . '/database/factories/';

function updateFactory($className, $definition) {
    global $factoriesDir;
    $file = $factoriesDir . $className . 'Factory.php';
    if (!file_exists($file)) return;

    $content = file_get_contents($file);
    
    // Replace empty array with definition
    $content = preg_replace('/return \[(\s+)\];/', "return [\n$definition\n        ];", $content);

    file_put_contents($file, $content);
}

updateFactory('Role', "            'name' => \$this->faker->unique()->word(),\n            'guard_name' => 'web',");
updateFactory('Permission', "            'name' => \$this->faker->unique()->word(),\n            'guard_name' => 'web',");
updateFactory('Branch', "            'name' => \$this->faker->company() . ' Şubesi',\n            'slug' => \$this->faker->unique()->slug(),\n            'phone' => \$this->faker->phoneNumber(),\n            'email' => \$this->faker->companyEmail(),\n            'address' => \$this->faker->address(),");
updateFactory('Media', "            'model_type' => 'App\\\\Models\\\\User',\n            'model_id' => 1,\n            'collection_name' => 'default',\n            'name' => \$this->faker->word(),\n            'file_name' => \$this->faker->word() . '.jpg',\n            'disk' => 'public',\n            'size' => \$this->faker->numberBetween(1000, 100000),\n            'manipulations' => json_encode([]),\n            'custom_properties' => json_encode([]),\n            'generated_conversions' => json_encode([]),\n            'responsive_images' => json_encode([]),");
updateFactory('Document', "            'title' => \$this->faker->sentence(3),\n            'description' => \$this->faker->paragraph(),\n            'file_path' => '/documents/' . \$this->faker->word() . '.pdf',\n            'type' => \$this->faker->randomElement(['internal', 'public']),");
updateFactory('Setting', "            'key' => \$this->faker->unique()->word(),\n            'value' => \$this->faker->word(),\n            'group' => 'general',");
updateFactory('Page', "            'title' => \$this->faker->sentence(3),\n            'slug' => \$this->faker->unique()->slug(),\n            'content' => \$this->faker->paragraphs(3, true),\n            'is_published' => true,\n            'meta_title' => \$this->faker->sentence(4),\n            'meta_description' => \$this->faker->sentence(),");
updateFactory('Slider', "            'title' => \$this->faker->sentence(3),\n            'image_path' => '/assets/branding/og-image.jpg',\n            'link' => null,\n            'order' => \$this->faker->numberBetween(1, 10),\n            'is_active' => true,");
updateFactory('BlogCategory', "            'name' => \$this->faker->word(),\n            'slug' => \$this->faker->unique()->slug(),\n            'description' => \$this->faker->sentence(),");
updateFactory('Blog', "            'title' => \$this->faker->sentence(4),\n            'slug' => \$this->faker->unique()->slug(),\n            'content' => \$this->faker->paragraphs(4, true),\n            'image_path' => '/assets/branding/og-image.jpg',\n            'is_published' => true,\n            'published_at' => now(),");
updateFactory('Event', "            'title' => \$this->faker->sentence(4),\n            'slug' => \$this->faker->unique()->slug(),\n            'description' => \$this->faker->paragraph(),\n            'event_date' => \$this->faker->dateTimeBetween('now', '+2 months'),\n            'location' => \$this->faker->address(),\n            'image_path' => '/assets/branding/og-image.jpg',");
updateFactory('Announcement', "            'title' => \$this->faker->sentence(4),\n            'slug' => \$this->faker->unique()->slug(),\n            'content' => \$this->faker->paragraph(),\n            'is_active' => true,\n            'published_at' => now(),");
updateFactory('Gallery', "            'title' => \$this->faker->word(),\n            'description' => \$this->faker->sentence(),\n            'image_path' => '/assets/branding/og-image.jpg',\n            'order' => \$this->faker->numberBetween(1, 10),\n            'is_active' => true,");
updateFactory('Teacher', "            'title' => \$this->faker->jobTitle(),\n            'bio' => \$this->faker->paragraph(),\n            'specialities' => \$this->faker->word(),");
updateFactory('Course', "            'name' => \$this->faker->words(3, true),\n            'slug' => \$this->faker->unique()->slug(),\n            'description' => \$this->faker->paragraph(),\n            'price' => \$this->faker->randomFloat(2, 1000, 10000),\n            'duration' => '12 Months',\n            'is_active' => true,");
updateFactory('Classroom', "            'name' => 'Sınıf ' . \$this->faker->numberBetween(10, 99),\n            'capacity' => 12,");
updateFactory('Registration', "            'student_name' => \$this->faker->name(),\n            'student_phone' => \$this->faker->phoneNumber(),\n            'parent_name' => \$this->faker->name(),\n            'parent_phone' => \$this->faker->phoneNumber(),\n            'grade' => \$this->faker->randomElement(['9', '10', '11', '12', 'Mezun']),\n            'program' => \$this->faker->randomElement(['VIP Butik', 'Standart', 'Özel Ders']),\n            'status' => \$this->faker->randomElement(['pending', 'approved', 'rejected']),");
updateFactory('Lead', "            'name' => \$this->faker->name(),\n            'phone' => \$this->faker->phoneNumber(),\n            'email' => \$this->faker->safeEmail(),\n            'source' => \$this->faker->randomElement(['web', 'facebook', 'instagram', 'referans']),\n            'status' => \$this->faker->randomElement(['new', 'contacted', 'qualified', 'lost']),\n            'notes' => \$this->faker->sentence(),");
updateFactory('ContactMessage', "            'name' => \$this->faker->name(),\n            'email' => \$this->faker->safeEmail(),\n            'phone' => \$this->faker->phoneNumber(),\n            'subject' => \$this->faker->sentence(3),\n            'message' => \$this->faker->paragraph(),\n            'is_read' => false,");

echo "Factories updated successfully.\n";
