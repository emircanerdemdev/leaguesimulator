@extends('layouts.master')
@section('content')
    <div class="container">
        <form action="{{ route('post.reset.data') }}" method="post">
            @csrf
            <button type="submit" class="btn-link">Reset All Data</button>
        </form>
        @if ($errors->any())
            {{ implode('', $errors->all()) }}
        @endif
        <div class="row justify-content-center">
            <div class="col-md-6">
                @if (isset($pointTables))
                    <div class="card">
                        <div class="card-body">
                            <p> Point Table </p>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Team Name</th>
                                        <th>Match Played</th>
                                        <th>Point</th>
                                        <th>Win</th>
                                        <th>Draw</th>
                                        <th>Lose</th>
                                        <th>GD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pointTables as $pointTable)
                                        <tr>
                                            <td>{{ $pointTable->name }}</td>
                                            <td>{{ $pointTable->played }}</td>
                                            <td>{{ $pointTable->point }}</td>
                                            <td>{{ $pointTable->win }}</td>
                                            <td>{{ $pointTable->draw }}</td>
                                            <td>{{ $pointTable->lose }}</td>
                                            <td>{{ $pointTable->goal_difference }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if (isset($championshipChances))
                    <div class="card">
                        <div class="card-body">
                            <p> Championship chances </p>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Team Name</th>
                                        <th>Chance (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($championshipChances as $key => $value)
                                        <tr>
                                            <td>{{ $key }}</td>
                                            <td>{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @if (is_null($teams) || count($teams) < 1)
                            <p> There is no team record. Click <a href="{{ route('get.team.index') }}">here</a>
                                to create teams </p>
                        @else
                            @if (is_null($allFixtures) || count($allFixtures) < 1)
                                <p> There is no fixure record. Click <a href="{{ route('get.fixture.create') }}">here</a>
                                    to create fixture </p>
                            @else
                                @foreach ($allFixtures as $weekFixtures)
                                    {{ $weekFixtures[0]->week->name }}
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th><a
                                                        href="{{ route('get.fixture.play.week', ['weekId' => $weekFixtures[0]->week->id]) }}">
                                                        Play
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($weekFixtures as $fixture)
                                                <tr>
                                                    <td>{{ $fixture->home->name }}</td>
                                                    <td>{{ $fixture->opponent_one_score }}</td>
                                                    <td>{{ $fixture->opponent_two_score }}</td>
                                                    <td>{{ $fixture->away->name }} </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
