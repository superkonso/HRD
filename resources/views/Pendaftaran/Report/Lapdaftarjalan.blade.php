<table style="border:solid" width=100%>
    <thead>
        <tr>
        <th>NO</th>
        <th>NAMA</th>
        <th>ALAMAT</th>
        </tr>
    </thead>
        <tbody>
           @foreach ($pasiens as $data)
        <tr>
        <td>{{$data->id}}</td>
        <td>{{$data->TPasien_Nama}}</td>
        <td>{{$data->TPasien_Alamat}}</td>
        </tr>
        @endforeach
        </tbody>
</table>