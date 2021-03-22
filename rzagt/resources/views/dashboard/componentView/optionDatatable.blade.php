@push('vendor_css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/r-2.2.6/datatables.min.css"/>
@endpush

@push('page_vendor_js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/r-2.2.6/datatables.min.js"></script>
 
@endpush


@push('custom_js')
<script>
	$(document).ready(function () {
		$('#mytable').DataTable({
			dom: 'flBrtip',
			responsive: true,
			order: [[0, 'desc']],
			buttons: [
				'csv', 'pdf', 'print', 'excel'
			]
		});
	});
</script>
@endpush