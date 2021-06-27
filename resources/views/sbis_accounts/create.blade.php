@extends('partials.layout')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Аккаунт sbis'])
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body">
                    <form action="{{route('sbisAccounts.store')}}" method="post" id="added_form">
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
                                   value="" required>
                        </div>
                        <div class="form-group">
                            <label for="app_secret">защищенный ключ</label>
                            <input type="text" class="form-control" id="app_secret" name="app_secret"
                                   placeholder="защищенный ключ" value="">
                        </div>
                        <div class="form-group">
                            <label for="secret_key">сервисный ключ</label>
                            <input type="file" class="form-control" id="secret_key" name="secret_key"
                                   placeholder="сервисный ключ" required>
                        </div>
                        <div class="form-check mb-5">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1" value="1" name="status"
                                   checked>
                            <label class="form-check-label" for="exampleCheck1">Включить</label>
                        </div>
                        <div class="form-check mb-5">
                            <input type="checkbox" class="form-check-input" id="create_lead" value="1" name="create_lead"
                                   checked>
                            <label class="form-check-label" for="create_lead">Создавать лид?</label>
                        </div>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
