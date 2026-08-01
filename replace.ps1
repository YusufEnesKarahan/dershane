$replacements = @{
    "HQTenant" = "Institution"
    "hq_tenants" = "institutions"
    "hq_tenant_id" = "institution_id"
    "HQSubscription" = "InstitutionPlan"
    "hq_subscriptions" = "institution_plans"
    "HQInvoice" = "Invoice"
    "hq_invoices" = "invoices"
    "HQOnboardingFlow" = "InstitutionRegistration"
    "hq_onboarding_flows" = "institution_registrations"
    "HQAuditService" = "AuditService"
    "HQSchedulerService" = "SchedulerService"
    "HQPermission" = "Permission"
    "hq_permissions" = "permissions"
    "HQRole" = "Role"
    "hq_roles" = "roles"
    "HQAccessPolicy" = "AccessPolicy"
    "hq_access_policies" = "access_policies"
    "App\Domain\HQ\Services" = "App\Core\Services"
}

$files = Get-ChildItem -Path . -Recurse -Include *.php, *.md | Where-Object { $_.FullName -notmatch "vendor|node_modules|\.git|storage" }

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $modified = $false

    foreach ($key in $replacements.Keys) {
        if ($content -match [regex]::Escape($key)) {
            $content = $content -replace [regex]::Escape($key), $replacements[$key]
            $modified = $true
        }
    }

    if ($modified) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Modified $($file.FullName)"
    }
}
