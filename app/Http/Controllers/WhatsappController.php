<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Service;
use App\Models\User;
use App\Models\Whatsapp;
use App\Services\Wazzup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WhatsappController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return view('whatsapp.index');

    }

    public function indexAjax(Request $request)
    {

        $datas = Whatsapp::with(['user']);

        if (Auth::user()->hasRole(User::CLIENT)) {
            $datas->where('user_id', Auth::id());
        } else {
            if (!empty($request->get('user'))) {
                $datas->where('user_id', $request->get('user'));
            }
        }

        return DataTables::eloquent($datas)
            ->addColumn('action', function ($data) {
                $btns = '<a href="javascript:void(0)"  onclick="Delete(' . $data->id . ')" class="btn btn-danger"><i class="fa fa-trash-o"></i> Удалить</a>';
                $btns .= ' <a href="' . url('whatsapp/' . $data->id . '/edit') . '"  class="btn btn-warning"><i class="fa fa-pencil-square-o"></i> Изменить</a>';
                return $btns;
            })
            ->addColumn('chat', function ($data) {
                return '<a href="' . route('whatsapp.show', $data->id) . '" target="_blank">Открыть</a>';
            })
            ->editColumn('status', function ($data) {
                if ($data->status == Whatsapp::ENABLED) {
                    return 'Включен';
                }
                return 'Отключен';
            })
            ->rawColumns(['action', 'chat'])
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

        return view('whatsapp.create', compact('users'));
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
            'username' => 'required|string|max:255',
            'wazzup_id' => 'required|string|max:255',
            'api_key' => 'required|string|max:255',
            'status' => 'nullable|boolean',
            'channel_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return Response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $whatsapp = Whatsapp::where('user_id', Auth::id())->count();

        if ($whatsapp > 0){
            return  response()->json([
                'success' => false,
                'error' => 'У вас уже существует аккаунт'
            ], 403);
        }

        $data = $request->all();

        if (is_null($request->status)) {
            $data['status'] = 0;
        }

        if (Auth::user()->hasRole(User::CLIENT)) {
            $data['user_id'] = Auth::id();
        }

        $wazzup = Wazzup::updateOrCreate($request->api_key,$request->wazzup_id, $request->username);

        if (!$wazzup['success']) {
            return response()->json([
                'success' => false,
                'errors' => $wazzup['errors']

            ], 500);
        }

        Whatsapp::create($data);

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
        $account = Whatsapp::where('id', $id)
            ->where('status', Account::ENABLED)
            ->where('user_id', Auth::id());

        $account = $account->first();

        if (is_null($account)) {
            abort(404);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $account->api_key
        ])->post('http://api.wazzup24.com/v2/iframe', [
            'user' => [
                'id' => $account->wazzup_id,
                'name' => $account->username
            ],
            'scope' => 'global'
        ]);

        if ($response->status() !== 200) {
            return back()->withErrors($response->object()->errors);
        }

        return redirect()->to($response->object()->url);
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
        $user = Whatsapp::where('id', $id);
        $users = [];

        if (Auth::user()->hasRole(User::CLIENT)) {
            $user->where('user_id', Auth::id());
        } else {
            $users = User::whereHas('roles', function (Builder $builder) {
                $builder->where('name', 'Client');
            })->get();
        }

        $data = $user->first();

        if (is_null($data)) {
            abort(404);
        }

        return view('whatsapp.edit', compact('users', 'data'));
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
            'username' => 'required|string:255',
            'wazzup_id' => 'required|string:255',
            'api_key' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return Response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $data = $request->except(['_token']);

        if (is_null($request->status)) {
            $data['status'] = 0;
        }

        $wazzup = Wazzup::updateOrCreate($request->wazzup_id, $request->username, $request->api_key);

        if (!$wazzup['success']) {
            return response()->json([
                'success' => false,
                'errors' => $wazzup['errors']

            ], 500);
        }

        $account = Whatsapp::where('id', $id);

        if (Auth::user()->hasRole(User::CLIENT)) {
            $account->where('user_id', Auth::id());
        }

        $account->update($data);

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
        $account = Whatsapp::where('id', $id);

        if (Auth::user()->hasRole(User::CLIENT)) {
            $account->where('user_id', Auth::id());
        }

        return Response()->json($account->delete());
    }

    public function sendMessage(Request $request)
    {

    }
}
