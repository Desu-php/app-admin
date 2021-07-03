<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SbisAccount;
use App\Models\User;
use App\Models\Whatsapp;
use App\Services\Sbis;
use App\Services\Wazzup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('employees.index');
    }

    public function indexAjax()
    {
        $datas = User::with('employee')
        ->where('user_id', Auth::id())
        ->role('Employee');

        return DataTables::eloquent($datas)
            ->addColumn('action', function ($data) {
                $btns = '<a href="javascript:void(0)"  onclick="Delete(' . $data->id . ')" class="btn btn-danger">Удалить</a>';
                $btns .= ' <a href="' . url('employees/' . $data->id . '/edit') . '"  class="btn btn-warning">Изменить</a>';
                return $btns;
            })
            ->escapeColumns(null)
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Wazzup $wazzup)
    {
        //
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|max:255',
            'user_wazzup' => 'required|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $userData = $request->only(['email', 'password']);
        $userData['password'] = Hash::make($userData['password']);
        $userData['user_id'] = Auth::id();

        try {
            DB::beginTransaction();
            $user = User::create($userData);
            $user->assignRole('Employee');

            $wazzup_id = Str::uuid();
            $wazzup = $wazzup->updateOrCreate($wazzup_id, $request->user_wazzup);
            if (!$wazzup['success']) {
                return response()->json([
                    'success' => false,
                    'errors' => $wazzup['errors'][0]
                ], 500);
            }

            $employeeData = $request->except(['email', 'password']);
            $employeeData['user_id'] = $user->id;
            $employeeData['wazzup_id'] = $wazzup_id;

            Employee::create($employeeData);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }


        return response()->json([
            'success' => true,
            'message' => 'Сотрудник успешнл добавлен'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data = User::find($id);

        abort_if(is_null($data), 404);

        return view('employees.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wazzup $wazzup, $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,id,' . $id . '|max:255',
            'password' => 'nullable|confirmed|max:255',
            'user_wazzup' => 'required|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $userData = $request->only('email');
        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        try {
            DB::beginTransaction();

            $user = User::find($id);
            $user->update($userData);
            $wazzup = $wazzup->updateOrCreate($user->employee->wazzup_id, $request->user_wazzup);
            if (!$wazzup['success']) {
                return response()->json([
                    'success' => false,
                    'errors' => $wazzup['errors'][0]
                ], 500);
            }

            $employeeData = $request->except(['email', 'password', '_method', 'password_confirmation']);

            Employee::where('user_id', $id)->update($employeeData);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Сотрудник успешно обвнолен'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wazzup $wazzup,$id)
    {
        //
        $user = User::where('id', $id)
            ->where('user_id', Auth::id())->first();

        $result = $wazzup->deleteUser($user->employee->wazzup_id);

        if (!$result['success']){
            return response()->json([
                'success' => false,
                'errors' => $result['errors'][0]
            ], 500);
        }


        return Response()->json($user->delete());
    }
}
