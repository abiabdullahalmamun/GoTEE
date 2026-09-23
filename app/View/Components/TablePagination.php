<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;

class TablePagination extends Component
{
    public LengthAwarePaginator $paginator;
    public array $perPageOptions;

    public function __construct(LengthAwarePaginator $paginator, array $perPageOptions = [10, 15, 20, 30])
    {
        $this->paginator = $paginator;
        $this->perPageOptions = $perPageOptions;
    }

    public function render()
    {
        return view('components.table-pagination');
    }
}
