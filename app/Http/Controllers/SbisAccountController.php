<?php

namespace App\Http\Controllers;

use App\Models\SbisAccount;
use App\Models\User;
use App\Models\Whatsapp;
use App\Services\Sbis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SbisAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $sbis = new Sbis();
        $sbisAccpimt = SbisAccount::first();
        $token = $sbis->getToken($sbisAccpimt->app_client_id, $sbisAccpimt->app_secret, $sbisAccpimt->secret_key);
        dd($token);
        return view('sbis_accounts.index');
    }

    public function indexAjax(Request $request)
    {

        $datas = SbisAccount::with(['user']);

        if (Auth::user()->hasRole(User::CLIENT)) {
            $datas->where('user_id', Auth::id());
        } else {
            if (!empty($request->get('user'))) {
                $datas->where('user_id', $request->get('user'));
            }
        }

        return DataTables::eloquent($datas)
            ->addColumn('action', function ($data) {
                $btns = '<a href="javascript:void(0)"  onclick="Delete(' . $data->id . ')" class="btn btn-danger">Удалить</a>';
                $btns .= ' <a href="' . url('sbisAccounts/' . $data->id . '/edit') . '"  class="btn btn-warning">Изменить</a>';
//                $btns .= ' <a href="' . route('whatsapp.channel.create', $data->id) . '"  class="btn btn-primary">Привязать канал</a>';
                return $btns;
            })
            ->addColumn('chat', function ($data) {
                return '<a href="' . route('openChat') . '" target="_blank">Открыть</a>';
            })
            ->editColumn('status', function ($data) {
                if ($data->status == Whatsapp::ENABLED) {
                    return '<span class="badge badge-success">Включен</span>';
                }
                return '<span class="badge badge-danger">Отключен</span>';
            })
            ->rawColumns(['action', 'chat', 'status'])
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

        $users = [];
        if (Auth::user()->hasRole(User::SUPER_ADMIN)) {
            $users = User::whereHas('roles', function (Builder $builder) {
                $builder->where('name', 'Client');
            })->get();
        }

        return view('sbis_accounts.create', compact('users'));
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
            'user_id' => 'nullable|exists:users,id',
            'app_client_id' => 'required|string|max:255',
            'app_secret' => 'required|string|max:255',
            'secret_key' => 'required|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $sbisAccount = SbisAccount::where('user_id', Auth::id())->count();

        if ($sbisAccount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'У вас уже существует аккаунт'
            ], 403);
        }

        $data = $request->all();

        if (is_null($request->status)) {
            $data['status'] = 0;
        }

        if (Auth::user()->hasRole(User::CLIENT)) {
            $data['user_id'] = Auth::id();
        }

        $data['secret_key'] = file_get_contents($request->file('secret_key')->getRealPath());

        SbisAccount::create($data);

        return Response()->json([
            'success' => true,
            'message' => 'Вы успешно добавили аккаунт'
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

        $user = SbisAccount::where('id', $id);
        $users = [];

        if (Auth::user()->hasRole(User::CLIENT)) {
            $user->where('user_id', Auth::id());
        } else {
            $users = User::whereHas('roles', function (Builder $builder) {
                $builder->where('name', User::CLIENT);
            })->get();
        }

        $data = $user->first();

        if (is_null($data)) {
            abort(404);
        }

        return view('sbis_accounts.edit', compact('data'));
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
            'user_id' => 'nullable|exists:users,id',
            'app_client_id' => 'required|string|max:255',
            'app_secret' => 'required|string|max:255',
            'secret_key' => 'nullable|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }


        $data = $request->except(['_token', '_method']);

        if (is_null($request->status)) {
            $data['status'] = 0;
        }

        $sbisAccount = SbisAccount::where('id', $id);

        if (Auth::user()->hasRole(User::CLIENT)) {
            $data['user_id'] = Auth::id();
            $sbisAccount->where('user_id', Auth::id());
        }

        if ($request->hasFile('secret_key')) {
            $data['secret_key'] = file_get_contents($request->file('secret_key')->getRealPath());
        }

        $sbisAccount->update($data);

        return Response()->json([
            'success' => true,
            'message' => 'Вы успешно обновили аккаунт'
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
        $sbisAccount = SbisAccount::where('id', $id);
        if (Auth::user()->hasRole(User::CLIENT)) {
            $sbisAccount->where('user_id', Auth::id());
        }
        $sbisAccount->delete();
    }
}
