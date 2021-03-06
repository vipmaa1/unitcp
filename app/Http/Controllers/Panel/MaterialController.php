<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Material;
use App\Faculty;
use App\classes;
use DB;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CreateMaterial;

class MaterialController extends Controller
{

    public  function index(){
        return view('panel.material.all');

    }
    public function create()
    {
        return view('panel.material.create');
    }


    public function getData($id) {

//        dd($id);
        $classes = classes::where("faculty_id",$id)->get();
        $department = DB::table("departments")->where("faculty_id",$id)->get();
        $classes = DB::table("classes")->where("faculty_id",$id)->get();
        $materials = DB::table("materials")->where("faculty_id",$id)->get();
        $semesters = DB::table("semesters")->where("faculty_id",$id)->get();

//        $year = DB::table("year")->get();

        $data = ["classes"=>$classes,"department"=>$department,"materials"=>$materials,"semesters"=>$semesters,
            ];

        $view =view('panel.material.fac-selectors', $data)->render();

        return response()->json(['status'=>true,'item'=>$view]);
    }


    public function store(CreateMaterial $request)
    {

        $material = Material::create($request->all());

        return (isset($material)) ? $this->response_api(true, 'تم إضافة ماده دراسيه بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }


    public function edit($id)
    {
        $data['material'] = Material::find($id);
        return (isset($data['material'])) ? view('panel.material.edit', $data) : redirect()->route(get_current_locale() . '.panel.dashboard');
    }


    public function update($id, CreateMaterial $request)
    {
        $material = Material::updateOrCreate(['id' => $id], $request->all());

        return (isset($material)) ? $this->response_api(true, 'تم تعديل بيانات الكليه بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }



    public function delete($id)
    {
        $item = Material::find($id);
        return (isset($item) && $item->delete()) ? $this->response_api(true, 'تمت عملية الحذف بنجاح') : $this->response_api(false, 'حدث خطأ غير متوقع');
    }


    public function get_material_data_table(material $items)
    {
        $items = $items ->orderBy('id', 'ASC')->get();


        try {
            return DataTables::of($items)->editColumn('id', function ($item) {
                return $item->id;

            })->addColumn('action', function ($item) {
                return '<div class="row">
                      <a  title="Edit" style="margin-right: 10px"  href="' . lang_route('panel.material.edit', ['id' => $item->id]) . '"  class="btn btn-sm btn-primary edit" > <i style="margin-left: 3px" class="fa fa-check-square-o"></i> تعديل</a>
                      <a  data-toggle="reject" title="Delete" style="margin-right: 10px;background-color: #FA2A00;color:white"  data-url="' . admin_url('material/delete/' . $item->id) . '"   class="btn btn-sm btn-danger delete">  <i style="margin-left:3px" class="fa  fa-trash-o"></i> حذف </a>
                    </div>';
            })->rawColumns([ 'action'])->make(true);
        } catch (\Exception $e) {
            return $this->response_api(false, 'failed');
        }
    }



}
