$replacements = @{
    "HQTenant" = "Institution"
    "hq_tenants" = "institutions"
    "hq_tenant_id" = "institution_id"
    "HQAccessPolicy" = "AccessPolicy"
    "hq_access_policies" = "access_policies"
}

$file = "database\migrations\2026_07_30_085445_create_hq_iam_tables.php"
$content = Get-Content $file -Raw
$modified = $false

foreach ($key in $replacements.Keys) {
    if ($content -match [regex]::Escape($key)) {
        $content = $content -replace [regex]::Escape($key), $replacements[$key]
        $modified = $true
    }
}

if ($modified) {
    Set-Content -Path $file -Value $content -NoNewline
    Write-Host "Modified $($file)"
}
