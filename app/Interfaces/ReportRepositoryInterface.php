<?php

namespace App\Interfaces;

use App\Models\Shift;


interface ReportRepositoryInterface
{
    public function getDateWise(int $authId, string $dateVal, int | null $status);
}
