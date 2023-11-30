<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpPage;
use App\Utils\MyAppEnv;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class HelpPageController extends Controller
{
    private $_environment;

    /**
     * Create a new controller instance.
     * @param App $app
     * @return void
     */
    public function __construct(App $app)
    {
        $this->_environment = $app::environment();
    }

    public function index(Request $request)
    {
        if ($request->query('locale') == 'all' || $request->query('locale') === null) {
            $pages = HelpPage::query()->latest()->get();
        } else {
            $pages = HelpPage::query()
                ->where('country_id', $request->default_country->id)
                ->latest()
                ->get();
        }
        return view('admin.help.index', [
            'pages' => $pages,
        ]);
    }


    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function create(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return view('admin.help.create');
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $country_id = isset($request['default_country']) ? $request['default_country']['id'] : null;

            $is_exist = HelpPage::query()
                ->where('country_id', $country_id)
                ->first();
            if (!isset($is_exist)) {
                $page = HelpPage::create([
                    'description' => $request['description'],
                    'country_id' => $country_id,
                ]);
                if (!$page) {
                    return redirect()->back()->with('error_message', 'Something went wrong');
                } else {
                    DB::commit();
                    return redirect()->back()->with('success_message', 'Help Page created successfully');
                }
            } else {
                return redirect()->back()->with('error_message', 'Help  page already exist.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }


    /**
     * @param HelpPage $helpPage
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function edit(HelpPage $helpPage): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return \view('admin.help.edit', ['helpPage' => $helpPage]);
    }


    /**
     * @param Request $request
     * @param HelpPage $helpPage
     * @return RedirectResponse
     */
    public function update(Request $request, HelpPage $helpPage): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $helpPage->update([
                'description' => $request['description'],
            ]);
            DB::commit();
            return redirect()->back()->with('success_message', 'Help Page updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }


    /**
     * @param HelpPage $helpPage
     * @return RedirectResponse
     */
    public function destroy(HelpPage $helpPage): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $helpPage->delete();
            DB::commit();
            return redirect()->back()->with('success_message', 'Help Page deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }


}
