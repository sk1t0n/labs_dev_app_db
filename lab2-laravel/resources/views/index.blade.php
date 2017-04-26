@extends('base')

@section('title', 'Lab2')

@section('content')
<table border="1">
	<tr>
		<th>Id Category</th>
		<th>Id SPF</th>
		<th>Id PF</th>
		<th>Comment</th>
		<th>SoftlineSKU</th>
		<th>VendorSKU</th>
		<th>ProductDescription</th>
		<th>Version</th>
		<th>Language</th>
		<th>Full/Upgrade</th>
		<th>Box/Lic</th>
		<th>AE/COM</th>
		<th>Media</th>
		<th>OS</th>
		<th>License Level</th>
		<th>Point</th>
		<th>LicenseComment</th>
		<th>Retail</th>
	</tr>
	@foreach ($rows as $row)
    <tr>
    	<td>{{ $row->id_c }}</td>
    	<td>{{ $row->id_spf }}</td>
    	<td>{{ $row->id_pf }}</td>
    	<td>{{ $row->comment }}</td>
    	<td>{{ $row->softline_SKU }}</td>
    	<td>{{ $row->vendor_SKU }}</td>
    	<td>{{ $row->product_description }}</td>
    	<td>{{ $row->version }}</td>
    	<td>{{ $row->language }}</td>
    	<td>{{ $row->full_upgrade }}</td>
    	<td>{{ $row->box_lic }}</td>
    	<td>{{ $row->ae_com }}</td>
    	<td>{{ $row->media }}</td>
    	<td>{{ $row->os }}</td>
    	<td>{{ $row->license_level }}</td>
    	<td>{{ $row->point }}</td>
    	<td>{{ $row->license_comment }}</td>
    	<td>{{ $row->retail }}</td>
    </tr>
	@endforeach
</table>
{{ $rows->render() }}
@endsection