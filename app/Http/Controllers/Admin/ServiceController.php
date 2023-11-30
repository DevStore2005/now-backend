<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\Service;
use App\Models\SubService;
use App\Utils\ServiceType;
use App\Models\VehicleType;
use App\Http\Helpers\Common;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Models\ServiceContent;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{

    /**
     * Private variable to store the model
     * @var Service
     * @var SubService
     * @var ServiceContent
     * @access private
     */
    private $_service, $_sub_service, $_serviceContent, $_helper;

    /**
     * Create a new controller instance.
     * @param \App\Models\Service $service
     * @param \App\Models\SubService $sub_service
     * @param \App\Models\ServiceContent $serviceContent
     * @param \App\Http\Helpers\Common $helper
     * @return void
     */
    public function __construct(Service $service, SubService $sub_service, ServiceContent $serviceContent, Common $helper)
    {
        $this->_service = $service;
        $this->_sub_service = $sub_service;
        $this->_serviceContent = $serviceContent;
        $this->_helper = $helper;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function main_list(Request $request)
    {
        if ($request->query('locale') === 'all' || $request->query('locale') === null) {
            $data['data'] = $this->_service->orderBy('created_at', 'desc')->get();
        } else {
            $data['data'] = $this->_service->where('country_id', $request->default_country->id)->orderBy('created_at', 'desc')->get();
        }
        return view('admin.services.main_list', $data);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function main_create(Request $request)
    {
        if (isset($request->id)) {
            $request->validate([
                'name' => 'min:1|max:30',
                'image' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);
        } else {
            $request->validate([
                'name' => 'required|min:1|max:30',
                'image' => 'required|mimes:jpeg,png,jpg|max:4096',
            ]);
        }
        try {
            $this->_service->createService($request->all());
            return redirect()->back()->with('success_message', 'Service Added');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    public function main_update_status(Request $request, $status, $id)
    {
        $data = Service::find($id);
        if ($status == 'inactive') {
            $data->status = false;
        } else {
            $data->status = true;
        }
        $data->save();
        return redirect()->back()->with('success_message', 'Service updated');
    }

    public function sub_list(Request $request)
    {
        if ($request->query('locale') === 'all' || $request->query('locale') === null) {
            $data['services'] = Service::latest()->get();
            $data['data'] = SubService::query()
                ->with(['service'])
                ->latest()->paginate(100)->withQueryString();
        } else {
            $data['services'] = Service::query()->where('country_id', $request->default_country->id)->latest()->get();
            $data['data'] = SubService::query()
                ->with(['service'])
                ->whereIn('service_id', $data['services']->pluck('id')->toArray())
                ->latest()->paginate(100)->withQueryString();
        }
        return view('admin.services.sub_list', $data);
    }

    public function sub_create(Request $request)
    {
        if (isset($request->id) && !empty($request->id)) {
            $request->validate([
                'name' => 'min:1|max:30',
                'credit' => 'numeric|min:1|max:99999999',
                'service_id' => 'exists:services,id',
                'view_type' => 'required|in:provider,standard',
                'image' => 'mimes:jpeg,png,jpg|max:4096',
            ]);
        } else {
            $request->validate([
                'name' => 'required|min:1|max:30',
                'credit' => 'required|numeric|min:1|max:99999999',
                'service_id' => 'required|exists:services,id',
                'image' => 'required|mimes:jpeg,png,jpg|max:4096',
                'view_type' => 'required|in:provider,standard',
            ]);
        }
        $show_in_the_footer = filter_var($request->show_in_the_footer, FILTER_VALIDATE_BOOLEAN);

        $request->merge([
            'show_in_the_footer' => $show_in_the_footer
        ]);

        $this->_sub_service->createSubService($request->all());

        return redirect()->back()->with('success_message', 'Service updated');
    }

    public function sub_update_status(Request $request, $status, $id)
    {
        $data = SubService::find($id);
        if ($status == 'inactive') {
            $data->status = false;
        } else {
            $data->status = true;
        }
        $data->save();
        return redirect()->back()->with('success_message', 'Service updated');
    }

    public function content_list(Request $request, $type, $id)
    {

        $typeId = null;
        $table = null;

        if ($type == 'SUB_SERVICE') {
            $typeId = 'sub_service_id';
            $table = 'sub_services';
        }
        if ($type == ServiceType::SERVICE) {
            $typeId = 'service_id';
            $table = 'services';
        }

        $request->validate([
            $id => 'exists:' . $table . ',id',
        ]);

        $data = $this->_serviceContent->where($typeId, $id)->latest()->get();
        if ($table == 'services') {
            $title = 'Service Content';
            // if ($request->ajax()) {
            // 	return $this->success($data, 'Service Content', 200);
            // }
            return view('admin.services.service_content', compact('data', 'title', 'typeId', 'id'));
        }
        if ($table == 'sub_services') {
            $title = 'Sub Service Content';
            // if ($request->ajax()) {
            // 	return $this->success($data, 'Service Content', 200);
            // }
            return view('admin.services.service_content', compact('data', 'title', 'typeId', 'id'));
        }
        return;
    }

    /**
     * Create Content for sub servoce.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createServiceContent(Request $request)
    {
        $service = $request->has('service_id') ? ['service_id' => 'required|exists:sub_services,id'] : ['sub_service_id' => 'required|exists:sub_services,id'];

        $request->validate([
                'title' => 'required|min:1|max:100',
                'description' => 'required|min:1|max:500',
                'image' => 'required|mimes:jpeg,png,jpg,svg|max:4096',
            ] + $service);

        try {
            $data = $request->all(['title', 'description', 'image', $request->has('service_id') ? 'service_id' : 'sub_service_id']);
            $data['image'] = $this->_helper->store_media($request->image, 'service_content');
            $return = $this->_serviceContent->createServiceContent($data);
            if ($return) {
                return redirect()->back()->with('success_message', 'Service updated');
            }
            return redirect()->back()->with('error_message', 'Something went wrong');
        } catch (\Exception $e) {
            $this->_helper->delete_media($request->image);
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    /**
     * Update Content for sub servoce.
     *
     * @param Request $request
     * @param ServiceContent $serviceContent
     * @return RedirectResponse
     */
    public function updateServiceContent(Request $request, ServiceContent $serviceContent)
    {
        $request->validate([
            'title' => 'required|min:1|max:100',
            'description' => 'required|min:1|max:500',
            'image' => 'mimes:jpeg,png,jpg,svg|max:4096',
        ]);

        try {
            $data = $request->all(['title', 'description', 'sub_service_id']);
            if ($request->hasFile('image')) {
                if ($serviceContent->image) $this->_helper->delete_media($serviceContent->image);
                $data['image'] = $this->_helper->store_media($request->image, 'service_content');
            }
            $return = $this->_serviceContent->updateServiceContent($serviceContent, $data);
            if ($return) {
                return redirect()->back()->with('success_message', 'Service updated');
            }
            return redirect()->back()->with('error_message', 'Something went wrong');
        } catch (\Exception $e) {
            if ($request->hasFile('image')) {
                $this->_helper->delete_media($data['image']);
            }
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'Something went wrong');
        }
    }

    /**
     * delete content
     *
     * @param Request $request
     * @param ServiceContent $serviceContent
     */
    public function deleteServiceContent(Request $request, ServiceContent $serviceContent)
    {
        $this->_helper->delete_media($serviceContent->image);
        $serviceContent->delete();
        return redirect()->back()->with('success_message', 'Deleted Service Content');
    }

    /**
     * Destory Service
     *
     * @param int $id
     */
    public function destroy($type, $id)
    {
        try {
            if ($type == 'service') {
                $hasService = $this->_service->has('sub_services')->with(['sub_services' => function ($qry) {
                    return $qry->has('service_requests');
                }])->find($id);
                $service = $this->_service->find($id);
                if ($hasService == null || $hasService->sub_services->isEmpty()) {
                    $hasDeleted = $service->delete();
                    if ($hasDeleted) return redirect()->back()->with('success_message', 'Service deleted');
                    return redirect()->back()->with('error_message', 'Something went wrong');
                }
                $service->status = false;
                $service->save();
                return redirect()->back()->with('success_message', 'Service deactivated');
            }
            if ($type == "sub-service") {
                $hasSubservice = $this->_sub_service->has("service_requests")->find($id);
                $subservice = $this->_sub_service->find($id);
                if ($hasSubservice == null) {
                    $hasDeleted = $subservice->delete();
                    if ($hasDeleted) return redirect()->back()->with('success_message', 'Sub Service deleted');
                    return redirect()->back()->with('error_message', 'Something went wrong');
                }
                $subservice->status = false;
                $subservice->save();
                return redirect()->back()->with('success_message', 'Sub Service deactivated');
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', "Something went wrong!");
        }
    }

}
