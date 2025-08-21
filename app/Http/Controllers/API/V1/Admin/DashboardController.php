<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get last 3 orders sorted by creation date (latest first)
        $orders = Order::orderBy('created_at', 'desc')->take(3)->get();
        
        // Dashboard summary: total amounts and counts by status
        $dashboardData = Order::selectRaw("
            SUM(total) AS TotalAmount,
            SUM(IF(status='ordered', total, 0)) AS TotalOrderAmount,
            SUM(IF(status='delivered', total, 0)) AS TotalDeliveredAmount,
            SUM(IF(status='canceled', total, 0)) AS TotalCanceledAmount,
            COUNT(*) AS TotalOrders,
            SUM(IF(status='ordered', 1, 0)) AS OrderCount,
            SUM(IF(status='delivered', 1, 0)) AS DeliveredCount,
            SUM(IF(status='canceled', 1, 0)) AS CanceledCount
        ")->first();
        
        // Monthly data: join all months with orders data of the current year
        $monthlyDatas = DB::select("
            SELECT 
                M.id AS MonthNo,
                M.name AS MonthName,
                IFNULL(D.TotalAmount, 0) AS TotalAmount,
                IFNULL(D.TotalOrderAmount, 0) AS TotalOrderAmount,
                IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
            FROM month_names M
    
            LEFT JOIN (
                SELECT 
                    MONTH(created_at) AS MonthNo,
                    DATE_FORMAT(created_at, '%b') AS MonthName,
                    SUM(total) AS TotalAmount,
                    SUM(IF(status = 'ordered', total, 0)) AS TotalOrderAmount,
                    SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                    SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount
                FROM orders
                WHERE YEAR(created_at) = YEAR(NOW())
                GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')
                ORDER BY MONTH(created_at)
            ) D ON D.MonthNo = M.id
        ");
    
        // Convert monthly amounts to comma-separated strings for chart usage
        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
        $orderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderAmount')->toArray());
        $deliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
        $canceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());
    
        // Calculate total sums for each type of amount across the year
        $totalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $totalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $totalOredredAmount = collect($monthlyDatas)->sum('TotalOrderAmount');
        $totalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');
    
        return response()->json([
            'orders' => $orders,
            'dashboardData' => $dashboardData,
            'monthlyData' => $monthlyDatas,
            'chart' => [
                'AmountM' => $AmountM,
                'orderedAmountM' => $orderedAmountM,
                'deliveredAmountM' => $deliveredAmountM,
                'canceledAmountM' => $canceledAmountM,
            ],
            'totals' => [
                'totalAmount' => $totalAmount,
                'totalDeliveredAmount' => $totalDeliveredAmount,
                'totalOredredAmount' => $totalOredredAmount,
                'totalCanceledAmount' => $totalCanceledAmount,
            ]
        ]);
    }
}
