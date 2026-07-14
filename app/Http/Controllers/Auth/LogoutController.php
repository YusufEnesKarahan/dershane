<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Actions\LogoutAction;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function destroy(Request $request, LogoutAction $action)
    {
        $action->execute();
        return redirect('/');
    }
}
