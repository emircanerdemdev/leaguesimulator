@extends('layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @if (is_null($teams) || count($teams) < 1)
                            <p> There is no team record. Click <a href="{{ route('get.fixture.seed') }}">here</a>
                                to create seed </p>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Team Name</th>
                                        <th><a href="{{ route('get.fixture.create') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-arrow-up-right" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z">
                                                    </path>
                                                </svg>
                                                Create Fixture
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teams as $team)
                                        <tr>
                                            <td> {{ $team->name }} </td>
                                            <td> </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
