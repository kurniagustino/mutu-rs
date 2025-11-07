<!DOCTYPE html>
<html>
<head>
    <title>Users Data</title>
    <style>
        /* Add some basic styling for the PDF */
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Users Data</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>NIP</th>
                    <th>Departemen</th>
                    <th>Roles</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\User::all() as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->NIP }}</td>
                        <td>{{ $user->departemen }}</td>
                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->updated_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button onclick="window.print()">Print to PDF</button>
    </div>
</body>
</html>
