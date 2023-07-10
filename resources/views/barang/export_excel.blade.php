<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Age</th>
            <th>Position</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($barangs as $index => $barang)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $barang->firstname }}</td>
                <td>{{ $barang->lastname }}</td>
                <td>{{ $barang->email }}</td>
                <td>{{ $barang->age }}</td>
                <td>{{ $barang->satuan->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
