<?php
$events = ['SubscriptionActivated', 'SubscriptionRenewed', 'SubscriptionExpired', 'SubscriptionSuspended', 'SubscriptionReactivated'];
foreach ($events as $event) {
    $content = "<?php\n\nnamespace App\\Events;\n\nuse App\\Models\\Subscription;\nuse Illuminate\\Broadcasting\\InteractsWithSockets;\nuse Illuminate\\Foundation\\Events\\Dispatchable;\nuse Illuminate\\Queue\\SerializesModels;\n\nclass $event\n{\n    use Dispatchable, InteractsWithSockets, SerializesModels;\n\n    public function __construct(public Subscription \$subscription) {}\n}\n";
    file_put_contents('app/Events/' . $event . '.php', $content);
}
