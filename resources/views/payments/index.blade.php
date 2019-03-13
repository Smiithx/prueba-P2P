@extends("layout.layout")
@section("content")
    {!! csrf_field() !!}
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="form-group text-right">
                <a href="{{url("/payments/create")}}" class="btn btn-primary">Registrar pago</a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="table-responsive">
            <div class="container-fluid">
                <table class="table responsive table-vcenter dataTable no-footer" id="tabla_payments">
                    <thead class="">
                    <tr>
                        <th>Referencia</th>
                        <th>Descripcion</th>
                        <th>Moneda</th>
                        <th class="text-center">Monto</th>
                        <th>Estado</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@section("title")
    Pagos
@endsection
@push("js")
@include("plugins.datatable")
<script>
    $(function () {
        var tabla_payments = $("#tabla_payments");

        function inicializar() {
            tabla_payments.DataTable({
                'ajax': {
                    "url": '{{url("/payments")}}',
                    "type": "GET",
                    dataSrc: '',
                },
                'columns': [
                    {data: 'reference'},
                    {data: 'description'},
                    {data: 'currency'},
                    {data: 'amount_format', className: "text-right"},
                    {data: 'status'},
                    {data: 'created_at', className: "text-center"},
                    {
                        render: function (data, type, row) {
                            var status = row.status;

                            var button = "";
                            if (status) {
                                if (status !== "APPROVED") {
                                    button = "<a href='" + row.process_url + "' class='btn btn-primary' data-toggle='tooltip' title='Pagar'><i class='glyphicon glyphicon-shopping-cart'></i></a>";
                                }
                            } else {
                                button = "<a href='{{url("/payments/response/")}}/" + row.reference + "' class='btn btn-primary' data-toggle='tooltip' title='Consultar'><i class='glyphicon glyphicon-info-sign'></i></a> ";
                            }

                            var transactions = "<button class='btn btn-info details-control' data-toggle='tooltip' title='Transacciones'><i class='glyphicon glyphicon-th-list'></i></button>";

                            return button + transactions;
                        },
                        className: "text-center"
                    },

                ]
            });

            tabla_payments.find("tbody").on('click', '.btn.details-control', function () {
                var tr = $(this).closest('tr');
                var row = tabla_payments.DataTable().row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        }

        inicializar();

        function format(rowData) {
            var div = $('<div/>')
                .addClass('loading')
                .text('Loading...');

            var transactions = rowData.transactions;

            var html = '<div class="well text-center">Sin registros</div>';

            if(transactions.length > 0){
                var trs = "";
                $.each(transactions,function(i,transaction){
                    trs += '<tr>\n' +
                            '<td>'+transaction.receipt+'</td>\n' +
                            '<td class="text-center">'+transaction.solved_in+'</td>\n' +
                            '<td class="text-right">'+transaction.discount_format+'</td>\n' +
                            '<td class="text-right">'+transaction.amount_format+'</td>\n' +
                            '<td>'+transaction.bank+'</td>\n' +
                            '<td class="text-center">'+transaction.status+'</td>\n' +
                        '</tr>\n';
                });

                html =
                    '<div class="container-fluid">\n' +
                    '<table class="table table-bordered">\n' +
                    '<thead>\n' +
                    '<tr>\n' +
                    '<th>Recibo</th>\n' +
                    '<th>Fecha</th>\n' +
                    '<th>Descuento</th>\n' +
                    '<th class="text-center">Monto</th>\n' +
                    '<th>Banco</th>\n' +
                    '<th class="text-center">Estado</th>\n' +
                    '</tr>\n' +
                    '</thead>\n' +
                    '<tbody>\n' +
                    trs +
                    '</tbody>\n' +
                    '</table>\n' +
                    '</div>\n';
            }



            div.html( html ).removeClass( 'loading' );

            return div;
        }

    });
</script>
@endpush