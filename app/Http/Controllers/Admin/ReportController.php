<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales()
    {
        // Untuk sekarang, hanya return view kosong
        return view('admin.reports.sales');
    }
}
