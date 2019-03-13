@push('css')
<link rel="stylesheet" href="{{asset('plugins/datatables/css/dataTables.bootstrap.css')}}">
<style>
    table.table tbody td:last-child .btn{
        margin-right: 5px;
        display: inline-block;
    }
</style>
@endpush

@push('js')
<script src="{{asset('plugins/datatables/js/jquery.dataTables.js')}}"></script>
<script src="{{asset('plugins/datatables/js/dataTables.bootstrap.js')}}"></script>
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'none';

        var config_datatable = {
            "language": {
                "paginate": {
                    "previous": "<span aria-hidden='true'>&laquo;</span>",
                    "next": "<span aria-hidden='true'>&raquo;</span>",
                },
                lengthMenu: 'Mostrar _MENU_ registros por página',
                zeroRecords: 'Sin resultados',
                info: 'Mostrando la página _PAGE_ de _PAGES_',
                infoEmpty: 'Sin registros disponibles',
                infoFiltered: '(filtrados de _MAX_ registros totales)',
                search: 'Buscar',
            },
            "fnDrawCallback": function( oSettings ) {
                reloadTooltips();
            },
        };

        $.extend($.fn.dataTable.defaults, config_datatable);
    });
</script>
@endpush
