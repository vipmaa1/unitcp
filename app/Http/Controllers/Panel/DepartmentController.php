<?php

namespace App\Http\Controllers\Panel;

use App\Department;
use App\Faculty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDepartment;
use Yajra\DataTables\DataTables;

class DepartmentController extends Controller
{
    public  function index(){
        return view('panel.department.all');

    }
    public function create()
    {
        $faculty= Faculty::get();
        return view('panel.department.create',compact('faculty'));
    }



    public function store(CreateDepartment $request)
    {
        $data= $request->except('_token');
        $department = Department::create($data);
        return (isset($department)) ? $this->response_api(true, 'تم إضافة قسم بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }


    public function edit($id)
    {
        $data['department'] = Department::find($id);
        return (isset($data['department'])) ? view('panel.department.edit', $data) : redirect()->route(get_current_locale() . '.panel.dashboard');
    }


    public function update($id, CreateDepartment $request)
    {
        $department = Department::updateOrCreate(['id' => $id], $request->all());

        return (isset($department)) ? $this->response_api(true, 'تم تعديل بيانات القسم بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }



    public function delete($id)
    {
        $item = department::find($id);
        return (isset($item) && $item->delete()) ? $this->response_api(true, 'تمت عملية الحذف بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }


    public function get_department_data_table(department $items)
    {
        $items = $items ->orderBy('id', 'ASC')->get();

        try {
            return DataTables::of($items)->editColumn('id', function ($item) {
                return $item->id;

            })->addColumn('action', function ($item) {
                return '<div class="row">
                      <a  title="Edit" style="margin-right: 10px"  href="' . lang_route('panel.department.edit', ['id' => $item->id]) . '"  class="btn btn-sm btn-primary edit" > <i style="margin-left: 3px" class="fa fa-check-square-o"></i> تعديل</a>
                      <a  data-toggle="reject" title="Delete" style="margin-right: 10px;background-color: #FA2A00;color:white"  data-url="' . admin_url('department/delete/' . $item->id) . '"   class="btn btn-sm btn-danger delete">  <i style="margin-left:3px" class="fa  fa-trash-o"></i> حذف </a>
                    </div>';
            })->rawColumns([ 'action'])->make(true);
        } catch (\Exception $e) {
            return $this->response_api(false, 'failed');
        }
    }



}
