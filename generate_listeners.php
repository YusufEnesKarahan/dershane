<?php
$listeners = ['LogSubscriptionHistory', 'SendSubscriptionNotification', 'ClearSubscriptionCaches'];
@mkdir('app/Listeners/Subscription', 0777, true);
foreach ($listeners as $listener) {
    $content = "<?php\n\nnamespace App\\Listeners\\Subscription;\n\nuse Illuminate\\Contracts\\Queue\\ShouldQueue;\nuse Illuminate\\Queue\\InteractsWithQueue;\n\nclass $listener implements ShouldQueue\n{\n    use InteractsWithQueue;\n\n    public function handle(object \$event): void\n    {\n        // TODO: Implement listener logic\n    }\n}\n";
    file_put_contents('app/Listeners/Subscription/' . $listener . '.php', $content);
}
