<?php
namespace App\Events\System;
class ReportGeneratedEvent { public function __construct(public readonly int $reportId) {} }
