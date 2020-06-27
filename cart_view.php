<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="container">

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class='panel panel-title'><h4><b> &nbsp; &nbsp; VERIFICA TU COMPRA</h4></b></div>
                            <br>
                            <div class="box box-solid">
                                <div class="box-body">
                                    <table class="table table-striped">
                                        <thead>
                                        <th></th>
                                        <th>IMAGEN</th>
                                        <th>NOMBRE</th>
                                        <th>PRECIO</th>
                                        <th width="20%">CANTIDAD</th>
                                        <th>CONSOLIDADO</th>
                                        </thead>
                                        <tbody id="tbody">
                                        </tbody>
                                    </table>                                         
                                </div>
                            </div>

                            <div class="panel panel-title"><h4><b>&nbsp;&nbsp;&nbsp;  ELIJE TU FORMA DE PAGO</b></h4S></div>
                            <br>
                            <div class="container">
                                <div class="row">



                                    <?php
                                    if (isset($_SESSION['user'])) {

                                        echo"<div class='col-sm-3'>";
                                        echo"<a href='#addformtd' data-toggle='modal' class='btn  btn-sm '><img src='images/transferecncia.png' width='80px'><span class='label label-primary badge'><b>TRANSFERENCIA O DEPOSITO</b></span></a>";
                                        echo "</div>";


                                        echo"<div class='col-sm-3'>";
                                        echo"<a href='#addformpad' data-toggle='modal' class='btn  btn-sm '><img src='images/pagoce.png' width='80px'><span class='label label-primary badge'><b>&nbsp; &nbsp;&nbsp; &nbsp;  PAGO CONTRA ENTREGA &nbsp;</b></span></a>";
                                        echo "</div>";


                                        echo "
                                            <div class='col-sm-3' id='paypal-button'><img src='images/espacio.png' width='32px' ></div>
	        				";
                                    } else {
                                        echo "
	        			   <h4>Debes <a href='login'>Iniciar sesión</a> para realizar compras.</h4>
	        				";
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <?php include 'includes/sidebar.php'; ?>
                        </div>
                    </div>

                </section>

            </div>
        </div>
        <?php $pdo->close(); ?>

        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
    <script>
        var total = 0;
        $(function () {
            $(document).on('click', '.cart_delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: 'cart_delete.php',
                    data: {id: id},
                    dataType: 'json',
                    success: function (response) {
                        if (!response.error) {
                            getDetails();
                            getCart();
                            getTotal();
                        }
                    }
                });
            });

            $(document).on('click', '.minus', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var qty = $('#qty_' + id).val();
                if (qty > 1) {
                    qty--;
                }
                $('#qty_' + id).val(qty);
                $.ajax({
                    type: 'POST',
                    url: 'cart_update.php',
                    data: {
                        id: id,
                        qty: qty,
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (!response.error) {
                            getDetails();
                            getCart();
                            getTotal();
                        }
                    }
                });
            });

            $(document).on('click', '.add', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var qty = $('#qty_' + id).val();
                qty++;
                $('#qty_' + id).val(qty);
                $.ajax({
                    type: 'POST',
                    url: 'cart_update.php',
                    data: {
                        id: id,
                        qty: qty,
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (!response.error) {
                            getDetails();
                            getCart();
                            getTotal();
                        }
                    }
                });
            });

            getDetails();
            getTotal();

        });

        function getDetails() {
            $.ajax({
                type: 'POST',
                url: 'cart_details.php',
                dataType: 'json',
                success: function (response) {
                    console.log(response)
                   //$('#tbody').html(response);
                    getCart();
                }
            });
        }

        function getTotal() {
            $.ajax({
                type: 'POST',
                url: 'cart_total.php',
                dataType: 'json',
                success: function (response) {
                    total = response;
                }
            });
        }
    </script>
    <!-- Paypal Express -->
    <script>
        paypal.Button.render({
            env: 'sandbox', // change for production if app is live,

            client: {
                sandbox: 'ASb1ZbVxG5ZFzCWLdYLi_d1-k5rmSjvBZhxP2etCxBKXaJHxPba13JJD_D3dTNriRbAv3Kp_72cgDvaZ',
                //production: 'AaBHKJFEej4V6yaArjzSx9cuf-UYesQYKqynQVCdBlKuZKawDDzFyuQdidPOBSGEhWaNQnnvfzuFB9SM'
            },

            commit: true, // Show a 'Pay Now' button

            style: {
                color: 'gold',
                size: 'small'
            },

            payment: function (data, actions) {
                return actions.payment.create({
                    payment: {
                        transactions: [
                            {
                                //total purchase
                                amount: {
                                    total: total,
                                    currency: 'USD'
                                }
                            }
                        ]
                    }
                });
            },

            onAuthorize: function (data, actions) {
                return actions.payment.execute().then(function (payment) {
                    window.location = 'sales?pay=' + payment.id;
                });
            },

        }, '#paypal-button');
    </script>



    <?php include 'payad_modal.php'; ?>
    <?php include './paytransf_modal.php'; ?>
</body>
</html>