@extends('partials.layout')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Аккаунт sbis'])
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body">
                    <form action="{{route('sbisAccounts.update', $data->id)}}" method="post" id="added_form">
                        @method('put')
                        @if(!empty($users))
                            <div class="form-group">
                                <label for="service" id="users">Клиенты</label>
                                <select class="form-control" id="users" name="user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->email}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="app_client_id ">ID приложения</label>
                            <input type="text" class="form-control" id="app_client_id" name="app_client_id"
                                   placeholder="ID приложения"
                                   value="{{$data->app_client_id}}" required>
                        </div>
                        <div class="form-group">
                            <label for="app_secret">защищенный ключ</label>
                            <input type="text" class="form-control" id="app_secret" name="app_secret"
                                   placeholder="защищенный ключ" value="{{$data->app_secret}}">
                        </div>

                        <div class="form-group">
                            <label for="secret_key">сервисный ключ</label>
                            <input type="file" class="form-control" id="secret_key" name="secret_key"
                                   placeholder="сервисный ключ">
                        </div>
                        <div class="form-group">
                            {{$data->secret_key}}
                        </div>
                        <div class="form-check mb-5">
                            <input type="checkbox"
                                   @if($data->status == \App\Models\SbisAccount::ENABLED)
                                   checked
                                   @endif
                                   class="form-check-input" id="exampleCheck1" value="1" name="status">
                            <label class="form-check-label"
                                   for="exampleCheck1">Включить</label>
                        </div>

                        <button type="submit" class="btn btn-success">Обновить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
