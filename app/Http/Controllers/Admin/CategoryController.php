<?php

namespace App\Http\Controllers\Admin;

use App\Utils\MyAppEnv;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{

    /**
     *  @var Category $_category 
     *  @var string $_environment
     */
    private $_category, $_environment;


    /**
     * Create a new controller instance.
     * @param  Category $category
     * @return void
     */
    public function __construct(Category $category, App $app)
    {
        $this->_category = $category;
        $this->_environment = $app::environment();
    }

    /**
     * User list
     *
     * @param Request $request
     * @return Response|View
     */
    public function index(Request $request)
    {
        try {
            $data = $this->_category->{$request->type}('type')->get();
            return view('admin.categories.index', compact('data'));
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $this->_category->createCategory($request->all());
        try {
            return redirect()->back()->with('success_message', 'Category Added');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * update status of category
     *
     * @param  Request  $request,
     * @param  string $status
     * @param  Category $category
     * @return Response
     */
    public function updateStatus(Request $request, $status, Category $category)
    {
        try {
            $category->updateCategory(['status' => $status], $category);
            return redirect()->back()->with('success_message', 'Status Updated');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * update category
     * @param Request $request
     * @param $id
     * @return Response|View
     */
    public function update(Request $request, Category $category)
    {
        try {
            $this->_category->updateCategory($request->all(), $category);
            return redirect()->back()->with('success_message', 'Category Updated');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * delete category
     * @param Request $request
     * @param Category $category
     * @return Response|View
     */
    public function destroy(Request $request, Category $category)
    {
        try {
            $category->destroy($category->id);
            return redirect()->back()->with('success_message', 'Category Deleted');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }
}
