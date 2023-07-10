<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pageTitle = 'Employee List';

        confirmDelete();

        return view('barang.index', compact('pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Barang';

        // ELOQUENT
        $satuans = Satuan::all();

        return view('barang.create', compact('pageTitle', 'satuans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'kodeBarang' => 'required',
            'namaBarang' => 'required',
            'hargaBarang' => 'required|numeric',
            'deskripsiBarang' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get File
        $file = $request->file('cv');

        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();

            // Store File
            $file->store('public/files');
        }

        // ELOQUENT
        $barang = New Barang;
        $barang->firstname = $request->kodeBarang;
        $barang->lastname = $request->namaBarang;
        $barang->email = $request->hargaBarang;
        $barang->age = $request->deskripsiBarang;
        $barang->satuan_id = $request->satuan;

        if ($file != null) {
            $barang->original_filename = $originalFilename;
            $barang->encrypted_filename = $encryptedFilename;
        }

        $barang->save();

        Alert::success('Added Successfully', 'Employee Data Added Successfully.');

        return redirect()->route('barangs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';

        $barang = Barang::with('satuan')
        ->where('id', $id)
        ->first();

        return view('barang.show', compact('pageTitle', 'barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Barang';

        // ELOQUENT
        $satuans = Satuan::all();
        $barangs = Barang::find($id);

        return view('barang.edit', compact('pageTitle', 'satuans', 'barangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $messages = [
            'required' => 'This field must be filled.',
            'numeric' => 'This field required a numeric data.'
        ];

        $validator = Validator::make($request->all(), [
            'kodeBarang' => 'required',
            'namaBarang' => 'required',
            'hargaBarang' => 'required',
            'deskripsiBarang' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get File
        $file = $request->file('cv');

        // ELOQUENT
        $barang = Barang::find($id);
        $barang->firstname = $request->kodeBarang;
        $barang->lastname = $request->namaBarang;
        $barang->email = $request->hargaBarang;
        $barang->age = $request->deskripsiBarang;
        $barang->satuan_id = $request->satuan;

        if ($file != null) {
            // Delete old file
            if ($barang->encrypted_filename && Storage::exists('public/files/' . $barang->encrypted_filename)) {
                Storage::delete('public/files/' . $barang->encrypted_filename);
            }

            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();

            // Store new file
            $file->store('public/files');

            $barang->original_filename = $originalFilename;
            $barang->encrypted_filename = $encryptedFilename;
        }

        $barang->save();

        Alert::success('Changed Successfully', 'Employee Data Changed Successfully.');

        return redirect()->route('barangs.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($employeeid)
    {

        $employee = Barang::find($employeeid);
        $encryptedFilename = 'public/files/'.$employee->encrypted_filename;

        if (Storage::exists($encryptedFilename)) {
            $deleted = Storage::delete($encryptedFilename);

        }

        $employee->delete();

        Alert::success('Deleted Successfully', 'Employee Data Deleted Successfully.');

        return redirect()->route('barangs.index');
    }

    public function downloadFile($employeeId)
    {
        $employee = Barang::find($employeeId);
        $encryptedFilename = 'public/files/'.$employee->encrypted_filename;
        $downloadFilename = Str::lower($employee->firstname.'_'.$employee->lastname.'_cv.pdf');

        if(Storage::exists($encryptedFilename)) {
        return Storage::download($encryptedFilename, $downloadFilename);
        }
    }

    public function getData(Request $request)
    {
        $employee = Barang::with('satuan');

        if ($request->ajax()) {
            return datatables()->of($employee)
                ->addIndexColumn()
                ->addColumn('actions', function($employee) {
                    return view('barang.actions', compact('employee'));
                })
                ->toJson();
        }
    }

    public function exportExcel()
    {
        return Excel::download(new EmployeesExport, 'barangs.xlsx');
    }

    public function exportPdf()
    {
        $barangs = Barang::all();

        $pdf = PDF::loadView('barang.export_pdf', compact('barangs'));

        return $pdf->download('employees.pdf');
    }
}
