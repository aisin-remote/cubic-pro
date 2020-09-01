<?php

namespace App\Http\DataTables;

use Yajra\DataTables\CollectionDataTable;

class CollectionCustom extends CollectionDataTable
{
    /**
     * Perform pagination.
     *
     * @return void
     */
    public function paging()
    {
        $this->collection = $this->collection->slice(
            $this->request->input(0),
            (int) $this->request->input('length') > 0 ? $this->request->input('length') : 10
        );
    }
}
