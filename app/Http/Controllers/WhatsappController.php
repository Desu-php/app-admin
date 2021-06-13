<?php

namespace App\Http\Controllers;

use App\Models\Account;
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
        $whatsapp = Whatsapp::where('user_id', Auth::id())->first();

        return view('whatsapp.index', compact('whatsapp'));

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
                $btns = '<a href="javascript:void(0)"  onclick="Delete(' . $data->id . ')" class="btn btn-danger">Удалить</a>';
                $btns .= ' <a href="' . url('whatsapp/' . $data->id . '/edit') . '"  class="btn btn-warning">Изменить</a>';
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
        ]);

        if ($validator->fails()) {
            return Response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $whatsapp = Whatsapp::where('user_id', Auth::id())->count();

        if ($whatsapp > 0) {
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

        $wazzup = Wazzup::updateOrCreate($request->api_key, $request->wazzup_id, $request->username);

        if (!$wazzup['success']) {
            return response()->json([
                'success' => false,
                'message' => $wazzup['errors'][0]

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
    public function openChat(Request $request)
    {
        //
        $account = Whatsapp::where('status', Whatsapp::ENABLED)
            ->where('user_id', Auth::id())->first();

        if (is_null($account)) {
            abort(404);
        }

        $scope = 'global';
        $filter = [];

        if ($request->has('number')) {

            if (!empty($request->text)) {
                $message = Wazzup::sendMessage($account->api_key, $account->channelId, $request->number, $request->text);
                if (!$message['success']) {
                    return response()->json($message, $message['status']);
                }
            }

            $scope = 'card';
            $filter = [
                'chatType' => 'whatsapp',
                'chatId' => $request->number,
                'activeChat' => [
                    'chatType' => 'whatsapp',
                    'chatId' => $request->number,
                ]
            ];
        }

        $data = Wazzup::openChat($account->api_key, $account->wazzup_id, $account->username, $scope, $filter);

        if (!$data['success']) {
            return response()->json($data, $data['status']);
        }

        return redirect()->to($data['data']->url);
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
                $builder->where('name', User::CLIENT);
            })->get();
        }

        $data = $user->first();

        if (is_null($data)) {
            abort(404);
        }

        $channels = Wazzup::getChannels($data->api_key);

        if (!$channels['success']) {
            return response()->json($channels, $channels['status']);
        }
        $channels = $channels['data'];

        return view('whatsapp.edit', compact('users', 'data', 'channels'));
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
            'channelId' => 'required|string'
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

        $wazzup = Wazzup::updateOrCreate($request->api_key, $request->wazzup_id, $request->username);

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

    public function channelCreate(Whatsapp $whatsapp)
    {
        if (is_null($whatsapp) || $whatsapp->user_id != Auth::id()) {
            abort(404);
        }

        $data = Wazzup::getChannels($whatsapp->api_key);

        if (!$data['success']) {
            return response()->json($data, $data['status']);
        }
        $channels = $data['data'];

        return view('whatsapp.create_channel', ['data' => $whatsapp, 'channels' => $channels]);
    }

    public function channelStore(Request $request, Whatsapp $whatsapp)
    {
        if (is_null($whatsapp) || $whatsapp->user_id != Auth::id()) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'channelId' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $whatsapp->channelId = $request->channelId;
        $whatsapp->save();

        return response()->json([
            'success' => true,
            'message' => 'Вы успешно привязали канал к аккаунту'
        ]);
    }
}
