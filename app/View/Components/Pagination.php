<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Pagination extends Component
{
    public $data;
    /**
     * Create a new component instance.
     * @param object $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.pagination', ['data' => $this->data]);
    }
}
