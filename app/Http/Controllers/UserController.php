<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('users.index');

    }

    public function indexAjax(Request $request)
    {

        $datas = User::query();
        return DataTables::eloquent($datas)
            ->addColumn('accounts', function ($data) {
                $btns = ' <a href="' . route('whatsapp.index','user='.$data->id). '"  class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Аккаунты</a>';
                return $btns;
            })
            ->addColumn('action', function ($data) {
                $btns = '<a href="javascript:void(0)"  onclick="Delete(' . $data->id . ')" class="btn btn-danger"><i class="fa fa-trash-o"></i> Удалить</a>';
                $btns .= ' <a href="' . url('users/' . $data->id . '/edit') . '"  class="btn btn-warning"><i class="fa fa-pencil-square-o"></i> Изменить</a>';
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
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|unique:users|email|max:255',
            'password' => ['required', 'string', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return Response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('Client');

        return Response()->json([
            'success' => true,
            'message' => 'Вы успешно добавили пользователя'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    public function showAjax()
    {

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
        $user = User::find($id);
        if (empty($user)) {
            abort(404);
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,id,' . $id,
            'password' => ['nullable', 'string', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return Response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }
        $user = User::find($id);

        if (empty($user)) {
            return Response()->json([
                'success' => false,
                'message' => 'Пользователь не найден'
            ], 404);
        }

        if (empty($request->password)) {
            $user->name = $request->name;
            $user->email = $request->email;
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return Response()->json([
            'success' => true,
            'message' => 'Вы успешно обновили пользователя'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::destroy($id);
        return Response()->json($user);
    }
}
