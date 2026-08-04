<?php

$data = json_decode(file_get_contents(__DIR__ . '/rcat_audit_data.json'), true);

echo "Total Routes Audited: " . count($data) . "\n\n";

$roleStats = [
    'Super Admin' => ['200' => 0, '302' => 0, '403' => 0, '404' => 0, '500' => 0],
    'Branch Admin' => ['200' => 0, '302' => 0, '403' => 0, '404' => 0, '500' => 0],
    'Teacher' => ['200' => 0, '302' => 0, '403' => 0, '404' => 0, '500' => 0],
    'Student' => ['200' => 0, '302' => 0, '403' => 0, '404' => 0, '500' => 0],
    'Parent' => ['200' => 0, '302' => 0, '403' => 0, '404' => 0, '500' => 0],
];

foreach ($data as $entry) {
    foreach ($entry['roles'] as $roleName => $status) {
        $st = (string)$status;
        if (!isset($roleStats[$roleName][$st])) {
            $roleStats[$roleName][$st] = 0;
        }
        $roleStats[$roleName][$st]++;
    }
}

print_r($roleStats);
