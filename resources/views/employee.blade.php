@extends('layout')

@section('content')
    <h3>Tableau de bord - {{ $employee->name }}</h3>
    <p>Mes dépenses</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Montant</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->amount }} €</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->spent_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge
                            @if($expense->status == 'APPROVED') bg-success
                            @elseif($expense->status == 'REJECTED') bg-danger
                            @elseif($expense->status == 'SUBMITTED') bg-warning
                            @else bg-secondary @endif">
                            {{ $expense->status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
