<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Material;
use App\Faculty;
use App\Semester;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CreateSemester;

class SemesterController extends Controller
{
    public function index(){
        return view('panel.semester.all');
    }

    public function create()
    {
        return view('panel.semester.create');

    }

    public function store(CreateSemester $request)
    {

        $semester = Semester::create($request->all());

        return (isset($semester)) ? $this->response_api(true, 'تم إضافة فصل دراسى جديد بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }



    public function edit($id)
    {
        $data['semester'] = Semester::find($id);
        return (isset($data['semester'])) ? view('panel.semester.edit', $data) : redirect()->route(get_current_locale() . '.panel.dashboard');
    }


    public function update($id, CreateSemester $request)
    {
        $semester = Semester::updateOrCreate(['id' => $id], $request->all());

        return (isset($semester)) ? $this->response_api(true, 'تم تعديل بيانات الفصل الدراسى بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }



    public function delete($id)
    {
        $item = semester::find($id);
        return (isset($item) && $item->delete()) ? $this->response_api(true, 'تمت عملية الحذف بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }


    public function get_semester_data_table(semester $items)
    {
        $items = $items ->orderBy('id', 'ASC')->get();
        try {
            return DataTables::of($items)->editColumn('id', function ($item) {
                return $item->id;

            })->addColumn('action', function ($item) {
                return '<div class="row">
                      <a  title="Edit" style="margin-right: 10px"  href="' . lang_route('panel.semester.edit', ['id' => $item->id]) . '"  class="btn btn-sm btn-primary edit" > <i style="margin-left: 3px" class="fa fa-check-square-o"></i> تعديل</a>
                      <a  data-toggle="reject" title="Delete" style="margin-right: 10px;background-color: #FA2A00;color:white"  data-url="' . admin_url('semester/delete/' . $item->id) . '"   class="btn btn-sm btn-danger delete">  <i style="margin-left:3px" class="fa  fa-trash-o"></i> حذف </a>
                    </div>';
            })->rawColumns([ 'action'])->make(true);
        } catch (\Exception $e) {
            return $this->response_api(false, 'failed');
        }
    }
}
