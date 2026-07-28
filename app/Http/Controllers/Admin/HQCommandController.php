<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQCommand;
use App\Domain\Platform\Services\HQCommandService;
use App\Domain\Platform\Services\HQHttpService;
use Illuminate\Http\Request;

class HQCommandController extends Controller
{
    public function __construct(
        protected HQCommandService $commandService,
        protected HQHttpService $httpService
    ) {}

    public function index()
    {
        $commands = HQCommand::orderBy('created_at', 'desc')->paginate(20);
        $statistics = $this->commandService->statistics();
        
        return view('admin.platform.commands.index', compact('commands', 'statistics'));
    }

    public function approve(HQCommand $command)
    {
        if ($this->commandService->approveCommand($command)) {
            return redirect()->route('admin.platform.commands.index')->with('success', 'Command approved successfully.');
        }

        return redirect()->route('admin.platform.commands.index')->with('error', 'Command could not be approved. (Must be pending)');
    }

    public function reject(HQCommand $command)
    {
        if ($this->commandService->rejectCommand($command)) {
            return redirect()->route('admin.platform.commands.index')->with('success', 'Command rejected successfully.');
        }

        return redirect()->route('admin.platform.commands.index')->with('error', 'Command could not be rejected. (Must be pending)');
    }

    public function execute(HQCommand $command)
    {
        if ($this->commandService->executeCommand($command)) {
            $command->refresh();
            // Try to report back to HQ asynchronously or via this proxy
            $this->httpService->sendCommandResult($command->command_uuid, $command->result);

            return redirect()->route('admin.platform.commands.index')->with('success', 'Command executed successfully.');
        }

        return redirect()->route('admin.platform.commands.index')->with('error', 'Command execution failed. See details.');
    }
}
