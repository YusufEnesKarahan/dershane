<?php

namespace App\Domain\Inventory\Services;

use App\Models\Asset;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\MaintenanceRecord;
use App\Models\AssetLocation;
use App\Models\Warehouse;

class InventoryAnalyticsService
{
    public function getDashboardStats()
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $maintenanceAssets = Asset::where('status', 'maintenance')->count();
        $assignedAssets = Asset::whereHas('assignments', function ($q) {
            $q->where('status', 'assigned');
        })->count();

        $totalStock = InventoryItem::sum('quantity');
        $criticalStock = InventoryItem::whereColumn('quantity', '<=', 'minimum_quantity')->count();

        $monthlyPurchaseSum = PurchaseOrder::where('status', 'approved')
            ->orWhere('status', 'completed')
            ->where('order_date', '>=', now()->startOfMonth()->toDateString())
            ->sum('total_amount');

        $maintenanceCost = MaintenanceRecord::where('status', 'completed')
            ->where('maintenance_date', '>=', now()->startOfMonth()->toDateString())
            ->sum('cost');

        // Location distributions
        $locations = AssetLocation::withCount('assets')->get();
        $locationDistribution = [];
        foreach ($locations as $loc) {
            $locationDistribution[] = [
                'name' => $loc->name,
                'count' => $loc->assets_count
            ];
        }

        return [
            'total_assets' => $totalAssets,
            'active_assets' => $activeAssets,
            'maintenance_assets' => $maintenanceAssets,
            'assigned_assets' => $assignedAssets,
            'total_stock' => $totalStock,
            'critical_stock' => $criticalStock,
            'monthly_purchase_sum' => $monthlyPurchaseSum,
            'maintenance_cost' => $maintenanceCost,
            'location_distribution' => $locationDistribution,
        ];
    }

    public function getAnalyticsReport()
    {
        $stats = $this->getDashboardStats();

        // Critical stock items
        $criticalItems = InventoryItem::with(['category', 'warehouse'])
            ->whereColumn('quantity', '<=', 'minimum_quantity')
            ->get();

        // Top items used (usage transactions)
        $topItems = InventoryItem::withCount(['transactions as usage_qty' => function ($q) {
            $q->where('type', 'usage')->select(\DB::raw('sum(quantity)'));
        }])->orderBy('usage_qty', 'desc')->take(5)->get();

        // Purchase order trends
        $orderTrends = PurchaseOrder::selectRaw("DATE_FORMAT(order_date, '%Y-%m') as label, sum(total_amount) as total")
            ->groupBy('label')
            ->orderBy('label', 'desc')
            ->take(6)
            ->get()
            ->toArray();

        return array_merge($stats, [
            'critical_items' => $criticalItems,
            'top_items' => $topItems,
            'order_trends' => $orderTrends,
        ]);
    }
}
