<!-- MESSAGE BOX-->
<div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
    <div class="mb-container">
        <div class="mb-middle">
            <div class="mb-title"><span class="fa fa-sign-out"></span> Se <strong>déconnecter</strong> ?</div>
            <div class="mb-content">
                <p>Etes vous sur de vouloir vous déconnecter ?</p>
            </div>
            <div class="mb-footer">
                <div class="pull-right">
                    <a href="../../logout.php" class="btn btn-success btn-lg">Oui</a>
                    <button class="btn btn-default btn-lg mb-control-close">Non</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MESSAGE BOX-->

<!-- START PRELOADS -->
<audio id="audio-alert" src="../../audio/alert.mp3" preload="auto"></audio>
<audio id="audio-fail" src="../../audio/fail.mp3" preload="auto"></audio>
<!-- END PRELOADS -->

<!-- START SCRIPTS -->
<!-- START PLUGINS -->
<script type="text/javascript" src="../../js/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../../js/plugins/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<!-- END PLUGINS -->

<!-- START THIS PAGE PLUGINS-->
<script type='text/javascript' src='../../js/plugins/icheck/icheck.min.js'></script>
<script type="text/javascript" src="../../js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="../../js/plugins/scrolltotop/scrolltopcontrol.js"></script>
<script type="text/javascript" src="../../js/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#customers2').DataTable({
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/French.json"
            }
        });
    });
</script>
<!-- END THIS PAGE PLUGINS-->

<!-- START TEMPLATE -->
<script type="text/javascript" src="../../js/settings.js"></script>

<script type="text/javascript" src="../../js/plugins.js"></script>
<script type="text/javascript" src="../../js/actions.js"></script>

<script type="text/javascript" src="../../datetimepicker/build/jquery.datetimepicker.full.js"></script>
<!-- validation formulaire -->
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.js"></script>
<script type="text/javascript">
    $("form").validate();
</script>
<!--fin validation formulaire -->


<script type="text/javascript">
    $('#date_foo').datetimepicker({
        formatTime: 'H:i',
        formatDate: 'd.m.Y',
        //defaultDate:'8.12.1986', // it's my birthday
        defaultDate: '+01.01.1970', // it's my birthday
        defaultTime: '08:00',
        timepickerScrollbar: false
    });
    $('#date_foo2').datetimepicker({
        formatTime: 'H:i',
        formatDate: 'd.m.Y',
        //defaultDate:'8.12.1986', // it's my birthday
        defaultDate: '+01.01.1970', // it's my birthday
        defaultTime: '08:00',
        timepickerScrollbar: false
    });
    $('#date_foo3').datetimepicker({
        formatTime: 'H:i',
        formatDate: 'd.m.Y',
        //defaultDate:'8.12.1986', // it's my birthday
        defaultDate: '+01.01.1970', // it's my birthday
        defaultTime: '08:00',
        timepickerScrollbar: false
    });
    function mySelectAction(select){
        var child2 = select.children[2];
        child2.className = (child2.className == "btn-group bootstrap-select form-control open") ? "btn-group bootstrap-select form-control": "btn-group bootstrap-select form-control open";
    }
    function mySelectValueChange(){
        var idAnnee = $("#annee_scolaire").val();
        $.ajax({
            type: "POST",
            url: "../changeAnneeScolaire.php",
            data: "idannee=" + idAnnee,
            success: function (data) {
                if (data == 1) {
                    window.location.reload();
                } else {
                    alert("ERREUR ! Changement de l'année scolaire non effectué.")
                }
            }
        });
    }

    (function anonyme(){
            var menu_open = $("li.active")[0].children[1].children;
            var link_open = window.location.pathname;
            for (var i = 0 ; i < menu_open.length ; i++){
                if(link_open == menu_open[i].children[0].pathname){
                    menu_open[i].children[0].style = "color: rgb(237, 95, 0);padding-left: 35px;";
                    break;
                }
            }
        }
    )()
</script>


<!-- END TEMPLATE -->
<!-- END SCRIPTS -->

</body>
</html>


