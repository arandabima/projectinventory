<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    use HasFactory;

    protected $fillable = ['report_type', 'period_start', 'period_end', 'generated_by', 'file_name'];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];
}
