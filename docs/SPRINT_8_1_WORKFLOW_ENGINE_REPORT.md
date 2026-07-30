# Sprint 8.1 - HQ Workflow Automation & Incident Response Engine

## Overview

The Workflow Engine sprint delivers a robust, event-driven automation platform within HQ Central. It enables administrators to create dynamic workflows consisting of nested conditions, varied actions, and delays, all orchestrated automatically based on system events such as `SystemOfflineDetected` or `AlertCreated`. 

The core architectural requirement to prevent any arbitrary remote code execution (RCE) on the central system was strictly adhered to. 

## Key Achievements

1. **Database & Core Models:**
   - Designed and built the necessary tables (`hq_workflows`, `hq_workflow_steps`, `hq_workflow_runs`, `hq_workflow_executions`, `hq_workflow_logs`) using a comprehensive migration.
   - Designed step relationships to support sequential execution.

2. **Event-Driven Architecture:**
   - Implemented `HQWorkflowEventSubscriber` to listen to 10+ core system events dynamically.
   - Events provide the tenant context and arbitrary payload variables directly to workflows.

3. **Workflow Services:**
   - **`WorkflowEngineService`**: Matches system events to active workflows and initiates `HQWorkflowRun` models. Logs an audit trail entry.
   - **`WorkflowExecutionService`**: Orchestrates the execution of steps sequentially by dispatching them to a Laravel Queue asynchronously. Manages error logging, status changes, and critical failure alerts.
   - **`WorkflowConditionService`**: Provides a dynamic, recursive evaluation engine that resolves deep condition rules (`AND`/`OR` nesting, `equals`, `greater_than`, `contains`, etc.) without running `eval()` or PHP logic evaluation strings.
   - **`WorkflowActionService`**: Supports multiple integrated actions such as sending emails, clearing cache, dispatching webhook payloads, creating alerts, creating audits, and executing remote commands through the secure `HQRemoteCommandService`.
   - **`WorkflowVariableResolver`**: Parses string values with `{{ placeholder }}` and safely swaps them out for data extracted from the run payload arrays.

4. **Queue Integration:**
   - Designed `ProcessWorkflowStepJob` to leverage Laravel's Queue system.
   - Allows complex, heavy, and delay-based steps to process independently and safely with retry strategies (`$tries = 3`, exponential backoff).

5. **API Endpoints:**
   - Exposed API routes to query workflow statuses, see historical runs, and manually trigger workflows based on events.
   - Secured behind `VerifyHQApiSignature` middleware.

6. **Admin Interface:**
   - Developed `HQWorkflowController` (Web).
   - Created beautiful, modern interface views: `index`, `create` (with a JSON configuration textarea builder), `show`, and `history`.

7. **Scheduling and Monitoring:**
   - Added robust workflow timeout monitoring to `HQSchedulerService` to catch and fail runs that might stall.
   - Extended `HQMonitoringService` to parse workflow execution counts and averages, which are directly rendered in the HQ Central Admin Dashboard under the "Workflow Automation" card.

8. **Testing:**
   - `WorkflowEngineTest` implemented as a robust feature test covering multiple assertions around variables, rule engines, queuing, and end-to-end event execution. Tests successfully pass.

## Code Quality & Rules Adherence
- **Zero RCE Policy strictly maintained.** No `exec`, `shell_exec`, `eval` exist in this module. Actions map only to safe, defined behaviors.
- Transactions are properly used when creating workflows.
- Implemented entirely within Laravel 13, PHP 8.4, and using the established Service Layer architecture. No Fat Controllers.
- All code runs harmoniously with existing Sprint requirements.
- No existing modules were refactored or broken.

**Status:** Completed
