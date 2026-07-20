$status1 = try { (Invoke-WebRequest -Uri 'http://127.0.0.1:8001/' -UseBasicParsing).StatusCode } catch { $_.Exception.Response.StatusCode.value__ }
Write-Host "ADIM 1 (Aktif Durumda HTTP Status Code): $status1"

# HQ Panel'de lisansi 'inactive' (pasif) yapiyoruz:
Set-Location "c:\Users\Yusuf Enes Karahan\Desktop\Scripts\hq-panel"
php artisan tinker --execute="App\Models\License::where('site_id', 3)->orWhere('id', 3)->update(['status' => 'inactive']);"

Set-Location "c:\Users\Yusuf Enes Karahan\Desktop\Scripts\dershane"
php artisan cache:clear --quiet

$status2 = try { (Invoke-WebRequest -Uri 'http://127.0.0.1:8001/' -UseBasicParsing).StatusCode } catch { $_.Exception.Response.StatusCode.value__ }
Write-Host "ADIM 2 (HQ Panel'den Pasif Yapildiginda HTTP Status Code): $status2"

# HQ Panel'de lisansi tekrar 'active' (aktif) yapiyoruz:
Set-Location "c:\Users\Yusuf Enes Karahan\Desktop\Scripts\hq-panel"
php artisan tinker --execute="App\Models\License::where('site_id', 3)->orWhere('id', 3)->update(['status' => 'active']);"

Set-Location "c:\Users\Yusuf Enes Karahan\Desktop\Scripts\dershane"
php artisan cache:clear --quiet

$status3 = try { (Invoke-WebRequest -Uri 'http://127.0.0.1:8001/' -UseBasicParsing).StatusCode } catch { $_.Exception.Response.StatusCode.value__ }
Write-Host "ADIM 3 (HQ Panel'den Tekrar Aktif Yapildiginda HTTP Status Code): $status3"
