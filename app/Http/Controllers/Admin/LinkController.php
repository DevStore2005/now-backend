<?php

namespace App\Http\Controllers\Admin;

use App\Models\Link;
use App\Models\User;
use App\Utils\MyAppEnv;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;

class LinkController extends Controller
{
    /**
     * @var User $_user
     * @var Link $_link
     * @var string $_environment
     *
     */
    private $_user, $_link, $_environment;


    /**
     * Create a new controller instance.
     * @param User $user
     * @param Link $link
     * @param App $app
     * @return void
     */
    public function __construct(User $user, Link $link, App $app)
    {
        $this->_user = $user;
        $this->_link = $link;
        $this->_environment = $app::environment();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        if ($request->query('locale') == 'all' || $request->query('locale') === null) {
            $links = $this->_link->where('page', '!=', null)->get();
        } else {
            $links = $this->_link->where('page', '!=', null)
                ->where('country_id', $request->default_country->id)->get();
        }
        return view('admin.links.index', [
            'links' => $links,
        ]);
    }


    /**
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function social(Request $request): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|Factory|Application
    {
        if ($request->query('locale') == 'all' || $request->query('locale') === null) {
            $links = $this->_link->where('type', '!=', null)->get();
        } else {
            $links = $this->_link->where('type', '!=', null)
                ->where('country_id', $request->default_country->id)->get();
        }
        return view('admin.links.social', [
            'links' => $links,
        ]);
    }

    /**
     * Display a listing of blog link.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function blog()
    {
        return view('admin.links.blog', [
            'links' => $this->_link->whereIs_blog(true)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreLinkRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLinkRequest $request)
    {
        try {
            $res = $this->_link->createLink($request->all());
            if ($res) {
                return redirect()->back()->with('success_message', 'Link created successfully!');
            }
            return redirect()->back()->with('error_message', 'Something went wrong!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Link $link
     * @return \Illuminate\Http\Response|null
     */
    public function show(Link $link)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateLinkRequest $request
     * @param \App\Models\Link $link
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLinkRequest $request, Link $link)
    {
        try {
            $res = $this->_link->updateLink($request->all(), $link);
            if ($res) {
                return redirect()->back()->with('success_message', 'Link updated successfully!');
            }
            return redirect()->back()->with('error_message', 'Link not updated!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Link $link
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
        try {
            $link = $link->delete();
            if ($link) {
                return redirect()->back()->with('success_message', 'Link deleted successfully!');
            }
            return redirect()->back()->with('error_message', 'Link not deleted!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }

    }
}
