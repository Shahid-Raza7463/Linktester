<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Proxy;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProxiesController extends Controller
{
    // Get data using yajra datatable
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Proxy::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $deleteUrl = 'proxy/' . $row['id'];
                    $editUrl = 'proxy/' . $row['id'] . '/edit';
                    //Get delete functionality
                    $form = '<div style="margin-left: 9px;"><form action="' . $deleteUrl . '" method="POST">';
                    $form .= csrf_field();
                    $form .= method_field('DELETE');
                    $form .= '<button type="submit" class="delete btn btn-danger btn-sm">Delete</button>';
                    $form .= '</form></div>';

                    $btn = "<div><a href=" . $editUrl . " class='edit btn btn-primary btn-sm' style='margin-left: 9px;'></i>Edit</a></div>" . $form;

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Admin.proxies.index');
    }

    // create proxy form
    public function create()
    {
        return view('Admin.proxies.create');
    }
    // // create proxy form
    // public function create()
    // {
    //     return view('Admin.proxies.create');
    // }

    // Store a newly created proxy in proxies table.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'iso' =>  "required",
            'ipAddress' =>  "required"
        ]);

        $proxy = new Proxy();
        $proxy->iso = $request['iso'];
        $proxy->ipAddress = $request['ipAddress'];
        $proxy->save();
        return redirect('proxy')->with('message', 'Your data successfully added');
    }


    // Edit proxy details
    public function edit(string $id)
    {
        $data = [];
        $data['proxy'] = Proxy::find($id);
        $data['id'] = $id;

        return view('Admin.proxies.update', $data);
    }

    // Update proxy data
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'iso' =>  "required",
            'ipAddress' =>  "required"
        ]);

        $proxy = Proxy::find($id);
        $proxy->iso = $request->input('iso');
        $proxy->ipAddress = $request->input('ipAddress');
        $proxy->save();
        return redirect('proxy')->with('message', 'Your data successfully updated');
    }

    // Delete Proxy data from table
    public function destroy(string $id)
    {
        $proxy = Proxy::find($id);
        $proxy->delete();
        return redirect('proxy')->with('message', 'Your data successfully deleted');
    }

    // Get bulk action functionality
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $selectedItemsJSON = $request->input('selected_items', '[]');
        $selectedItems = json_decode($selectedItemsJSON);

        if ($action === 'delete') {
            if (!empty($selectedItems)) {
                Proxy::whereIn('id', $selectedItems)->delete();
                return redirect()->back()->with('message', 'Selected items deleted successfully');
            } else {
                return redirect()->back()->with('message', 'No items selected for deletion');
            }
        }
        return redirect()->back()->with('message', 'Invalid bulk action');
    }
}
