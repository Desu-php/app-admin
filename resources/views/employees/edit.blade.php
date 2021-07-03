@extends('partials.layout')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Сотрудник'])
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body">
                    <form action="{{route('employees.update', $data->id)}}" method="post" id="added_form">
                        @method('put')
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="Email"
                                   value="{{$data->email}}" required>
                        </div>
                        <div class="form-group">
                            <label for="first_name">Имя</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   placeholder="Имя" value="{{$data->employee->first_name}}">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   placeholder="Фамилия" value="{{$data->employee->last_name}}" required>
                        </div>
                        <div class="form-group">
                            <label for="position">Должность</label>
                            <input type="text" class="form-control" id="position" name="position"
                                   placeholder="Должность" value="{{$data->employee->position}}" required>
                        </div>
                        <div class="form-group">
                            <label for="user_wazzup">Пользователь Wazzup</label>
                            <input type="text" class="form-control" id="user_wazzup" name="user_wazzup"
                                   placeholder="Пользователь Wazzup"
                                   value="{{$data->employee->user_wazzup}}"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="password">Придумайте пароль</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Подтвердите пароль</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Подтвердите пароль" value="">
                        </div>
                        <button type="submit" class="btn btn-success">Обновить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
