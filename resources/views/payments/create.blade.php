@extends("layout.layout")
@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="form-group text-right">
                <a href="{{url("payments")}}" class="btn btn-primary">Transacciones registradas</a>
            </div>
        </div>
    </div>
    <form action="{{url("/payments")}}" method="post" id="form_transaction">
        {{ csrf_field() }}
        <div class="row">
            <fieldset>
                <legend>Datos de compra:</legend>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="currency">Moneda</label>
                        <input type="text" name="currency" id="currency" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount">Monto</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01">
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="row">
            <div class="col-md-12 campos_pse">
                <div class="form-group">
                    <br>
                    <button class="btn btn-primary form-control" type="submit">Pagar</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@section("title")
    Compra
@endsection
@push("js")
@include("plugins.select2")
<script>
    $(function () {
        var form_transaction = $("#form_transaction");

        form_transaction.submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: form_transaction.attr("action"),
                data: form_transaction.serialize(),
                type: form_transaction.attr("method"),
                dataType: "json",
                beforeSend: function () {
                    form_transaction.find("button[type=submit]").button("loading");
                },
                success: function (res) {
                    console.log(res);
                    if(res.success){
                        window.location.href = res.process_url;
                    }else{
                        toastr.error(res.message);
                    }
                },
                error: function (e) {
                    console.log(e);
                    form_transaction.find("button[type=submit]").button("reset");
                    if (e.responseJSON.errors) {
                        $.each(e.responseJSON.errors, function (index, element) {
                            $.each(element, function (i, error) {
                                toastr.error(error);
                            });
                        });
                    } else {
                        toastr.error("Error!");
                    }

                },
                complete: function () {
                    form_transaction.find("button[type=submit]").button("reset");
                }
            });
        });
    });
</script>
@endpush