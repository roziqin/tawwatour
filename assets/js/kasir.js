(function($) {
    toastr.options.positionClass = 'toast-bottom-left';
    function countTotal(){
        var total = 0;
        $('.subtotal').each(function(){
            total += parseInt($(this).attr('value'));
        });
        var antar_jemput = $('input[name=antar_jemput]').val();
        antar_jemput = (antar_jemput)?antar_jemput:0;
        total += parseInt(antar_jemput);

        return total;
    }
    function countDiskon(){
        var diskon = 0;
        $('.diskon').each(function(){
            diskon += parseInt($(this).attr('value'));
        });
        return diskon;
    }
    function updateTotal(){
        var total = countTotal();
        var diskon = countDiskon();
        var total_belanja = total + diskon;
        if(total_belanja > 0)
        {
            if(diskon > 0)
                $('#total_belanja').html(formatAngka(total_belanja)+' <font class="black">( <i class="fa fa-cut"></i> -'+formatAngka(diskon)+')</font>');
            else
                $('#total_belanja').html(formatAngka(total_belanja));
        }
        else
        {
            $('#total_belanja').html('0');
        }

        $('#grand_total').attr('value',total);
        $('#grand_total').html(formatAngka(total));
        var bayar = parseInt($('input[name=bayar]').val());
        bayar = (bayar)?bayar:0;
        $('#dibayar').html(formatAngka(bayar));
        $('#kembalian').html(formatAngka(bayar-total));
    }
    function resetKeranjang(){
        $('#keranjang_belanja').find('tbody').html('');
        $('input[name=antar_jemput]').val('').trigger('keyup');
        $('input[name=bayar]').val('').trigger('keyup');
        updateTotal();
        $('#btn-no-invoice').html('No Invoice');
        $('#form-no-invoice').find('input[name=no_invoice]').val('');
        $('#form-no-invoice').hide();
        $('#btn-agen').html('Agen');
        $('#form-agen').find('input[name=username_agen]').val('');
        $('#form-agen').hide();
        $('input[name=nama_pelanggan]').val('');
        $('#nama_pelanggan').html('');
        $('input[name=hp_pelanggan]').val('');
        $('#hp_pelanggan').html('');
        $('textarea[name=alamat_pelanggan]').val('');
        $('#alamat_pelanggan').html('');
        $('input[name=kodepos_pelanggan]').val('');
        $('#form-pelanggan').hide();
        $('input[name=kode_produk]').focus();
        $('#btn-status-order').html('STATUS: BARU').attr('state','baru').removeClass('btn-success').addClass('btn-accent');
        $('#status-order').val(2);
        $('input[name^=item]').remove();
        $('input[name=tanggal]').val($('input[name=tanggal]').attr('placeholder'));
        $('input').val('');
    }
    $(document).on('keydown','input[name=kode_produk]','return', function() {
        var kode_produk = $(this).val();
        var url = $(this).attr('url');
        if(kode_produk==''){
            $('input[name=antar_jemput]').focus();
            return false;
        }
        $('#barcode_status').html('<i class="fa fa-circle-o-notch fa-spin orange fa-2x"></i>');
        var row = '';
        var existRow = $('table#keranjang_belanja').find('tbody').find('#kode'+kode_produk);
        if(existRow.length > 0)
        {
            var jumlah = parseInt(existRow.find('.jumlah_barang').val());
            jumlah++;
            existRow.find('.jumlah_barang').val(jumlah).trigger('keyup');
            $('#barcode_status').html('');
            $('input[name=kode_produk]').val('');
        }
        else
        {
            $.ajax({
                method: "GET",
                url: url+"/"+kode_produk,
                success: function(res){
                    if(res.status)
                    {
                        var item = res.item;
                        var diskon = item.diskon;
                        if($.type(diskon)=='object')
                        {
                            if(diskon.persen > 0)
                            {
                                diskon = parseFloat(diskon.persen);
                                diskon = (item.harga_produk * diskon)/100;
                            }
                            else
                            {
                                diskon = parseInt(diskon.nominal);
                            }
                        }
                        else
                        {
                            diskon = 0;
                        }
                        var subtotal = item.harga_produk - diskon;
                        row = '<tr id="kode'+item.id_produk+'"><td>'+item.id_produk+'</td><td>'+item.nama_produk+'</td><td><input type="number" class="form-control jumlah jumlah_barang" name="jumlah['+item.id_produk+']" min="1" value="1"></td><td class="harga" value="'+item.harga_produk+'">'+formatAngka(item.harga_produk)+'</td><td class="diskon" value="'+diskon+'" satuan="'+diskon+'">'+formatAngka(diskon)+'</td><td class="subtotal" value="'+subtotal+'">'+formatAngka(subtotal)+'</td><td align="center"><a href="#" class="btn-hapus-barang"><i class="fa fa-times red"></i></a></td></tr>';
                        if(row != '') $('table#keranjang_belanja').find('tbody').append(row); 
                        var hidden_input = '';
                        hidden_input += '<input type="hidden" name="item['+item.id_produk+'][id]" value="'+item.id_produk+'" />';
                        hidden_input += '<input type="hidden" name="item['+item.id_produk+'][nama]" value="'+item.nama_produk+'" />';
                        hidden_input += '<input type="hidden" name="item['+item.id_produk+'][harga_satuan]" value="'+item.harga_produk+'" />';
                        hidden_input += '<input type="hidden" name="item['+item.id_produk+'][jumlah]" value="1" />';
                        hidden_input += '<input type="hidden" name="item['+item.id_produk+'][subtotal]" value="'+subtotal+'" />';
                        if(diskon>0)
                        {
                            hidden_input += '<input type="hidden" name="item['+item.id_produk+'][diskon][value]" value="'+diskon+'" />';
                            hidden_input += '<input type="hidden" name="item['+item.id_produk+'][diskon][type]" value="'+item.diskon.status+'" />';
                            hidden_input += '<input type="hidden" name="item['+item.id_produk+'][diskon][event]" value="'+item.diskon.id_event+'" />';
                            hidden_input += '<input type="hidden" name="item['+item.id_produk+'][diskon][id]" value="'+item.diskon.id+'" />';
                        }
                        $('#form_belanja').append(hidden_input);
                        updateTotal();
                    }
                    else
                    {
                        toastr.error(res.message);
                    }
                    $('#barcode_status').html('');
                    $('input[name=kode_produk]').val('');
                },
                error: function(){
                    toastr.error('Koneksi Error');
                    $('#barcode_status').html('');
                    $('input[name=kode_produk]').val('');
                }
            });
        }
    });
    $(document).on('keyup change','.jumlah_barang',function(){
        var jumlah = parseInt($(this).val());
        var diskon_satuan = parseInt($(this).closest('tr').find('.diskon').attr('satuan'));
        if(!jumlah) jumlah = 0;
        var harga = parseInt($(this).closest('tr').find('.harga').attr('value'));
        var subtotal = jumlah*harga;
        var diskon = diskon_satuan*jumlah;
            subtotal -= diskon;
        var id_produk = $(this).closest('tr').attr('id');
            id_produk = id_produk.replace('kode', '');
        $("input[type=hidden][name='item["+id_produk+"][jumlah]']").val(jumlah);
        $("input[type=hidden][name='item["+id_produk+"][subtotal]']").val(subtotal);
        $("input[type=hidden][name='item["+id_produk+"][diskon][value]']").val(diskon);
        $(this).closest('tr').find('.subtotal').attr('value',subtotal).html(formatAngka(subtotal));
        $(this).closest('tr').find('.diskon').attr('value',diskon).html(formatAngka(diskon));
        updateTotal();
    });
    $(document).on('click','#btn-status-order',function(){
        var state = $(this).attr('state');
        if(state == 'baru')
        {
            $(this).html('STATUS: SELESAI');
            $(this).attr('state','selesai');
            $(this).removeClass('btn-accent');
            $(this).addClass('btn-success');
            $('#status-order').val(5);
        }
        else if(state == 'selesai')
        {
            $(this).html('STATUS: BARU');
            $(this).attr('state','baru');
            $(this).removeClass('btn-success');
            $(this).addClass('btn-accent');
            $('#status-order').val(2);
        }
    });
    $(document).on('click','#btn-no-invoice',function(){
        form_visible = $('#form-no-invoice').is(":visible");
        if(form_visible){
            var no_invoice = $('#form-no-invoice').find('input[name=no_invoice]').val();
            $('#btn-no-invoice').html('No Invoice');
            var url = $(this).attr('url');
            if(no_invoice != ''){
                $.ajax({
                    method: "GET",
                    url: url+"/"+no_invoice,
                    success: function(res){
                        if(res.status)
                        {
                            $('#btn-no-invoice').html(no_invoice);
                            $('#form-no-invoice').toggle();
                        }
                        else
                        {
                            toastr.error(res.message);
                        }
                    },
                    error: function(){
                        toastr.error('Koneksi Error');
                    }
                });
                return false;
            }
            $('#form-no-invoice').toggle();
        }
        else
        {
            $('#form-no-invoice').toggle();
            $('#form-no-invoice').find('input[name=no_invoice]').focus();
        }
    });
    $(document).on('keydown','input[name=no_invoice]','return', function() {
        $('#btn-no-invoice').trigger('click');
    });
    $(document).on('click','#btn-agen',function(){
        form_visible = $('#form-agen').is(":visible");
        if(form_visible){
            var username_agen = $('#form-agen').find('input[name=username_agen]').val();
            var url = $(this).attr('url');
            $('#btn-agen').html('Agen');
            if(username_agen != ''){
                $.ajax({
                    method: "GET",
                    url: url+"/"+username_agen,
                    success: function(res){
                        if(res.status)
                        {
                            $('#btn-agen').html('Agen : '+username_agen);
                            $('#form-agen').toggle();
                        }
                        else
                        {
                            toastr.error(res.message);
                        }
                    },
                    error: function(){
                        toastr.error('Koneksi Error');
                    }
                });
                return false;
            }
            $('#form-agen').toggle();
        }
        else
        {
            $('#form-agen').toggle();
            $('#form-agen').find('input[name=username_agen]').focus();
        }
    });
    $(document).on('keydown','input[name=username_agen]','return', function() {
        $('#btn-agen').trigger('click');
    });
    $(document).on('keyup blur change','input[name=kodepos_pelanggan]',function(){
        var kodepos = $(this).val();
        var url = $(this).attr('url');
        $.ajax({
            method: "GET",
            url: url+"/"+kodepos,
            success: function(res){
                $('input[name=antar_jemput]').val('');
                if(res.status)
                {
                    $('input[name=antar_jemput]').val(res.ongkir.nilai);
                }
                updateTotal();
            },
            error: function(){
            }
        });
    });
    $(document).on('keyup change mouseup','input[name=username_member]',function(){
        var username = $(this).val();
        var url = $(this).attr('url');
        $('#member_status').html('<i class="fa fa-circle-o-notch fa-spin orange"></i>');
        if(username)
        {
            $.ajax({
                method: "GET",
                url: url+"/"+username,
                success: function(res){
                    if(res.status)
                    {
                        var member = res.member;
                        var kelurahan = ucwords((member.kelurahan)?'Kel. '+member.kelurahan:'');
                        var kecamatan = ucwords((member.kecamatan)?'Kec. '+member.kecamatan:'');
                        var jenis = ucwords((member.jenis)?member.jenis:'');
                        var kabupaten = ucwords((member.kabupaten)?member.kabupaten:'');
                        var propinsi = ucwords((member.propinsi)?'Prov. '+member.propinsi:'');
                        $('#member_status').html('<i class="fa fa-check green"></i>');
                        $('input[name=nama_pelanggan]').val(ucwords(member.nama)).trigger('keyup');
                        $('input[name=hp_pelanggan]').val(member.hp).trigger('keyup');
                        $('textarea[name=alamat_pelanggan]').val(ucwords(member.alamat)+' '+kelurahan+' '+kecamatan+' '+jenis+' '+kabupaten+' '+propinsi).trigger('keyup');
                        $('input[name=kodepos_pelanggan]').val(member.kodepos).trigger('keyup');
                    }
                    else
                    {
                        $('#member_status').html('<i class="fa fa-times red"></i>');
                        $('input[name=nama_pelanggan]').val('').trigger('keyup');
                        $('input[name=hp_pelanggan]').val('').trigger('keyup');
                        $('textarea[name=alamat_pelanggan]').val('').trigger('keyup');
                        $('input[name=kodepos_pelanggan]').val('').trigger('keyup');
                    }
                },
                error: function(){
                    $('#member_status').html('<i class="fa fa-times red"></i>');
                }
            });
        }
        else
        {
            $('#member_status').html('');
        }
    });
    $(document).on('click','#btn-pelanggan',function(){
        $('#form-pelanggan').toggle();
        if($('#form-pelanggan').is(':visible')) {
            $('input[name=username_member]').focus();
        }
    });
    $(document).on('keyup blur change','input[name=nama_pelanggan]', function() {
        var input = $(this).val();
        $('#nama_pelanggan').html(input);
    });
    $(document).on('keyup blur change','input[name=hp_pelanggan]', function() {
        var input = $(this).val();
        $('#hp_pelanggan').html(input);
    });
    $(document).on('keyup blur change','textarea[name=alamat_pelanggan]', function() {
        var input = $(this).val();
        $('#alamat_pelanggan').html(input);
    });
    $(document).on('click','.btn-hapus-barang',function(){
        var id_produk = $(this).closest('tr').attr('id');
            id_produk = id_produk.replace('kode', '');
        $("input[type=hidden][name^='item["+id_produk+"]']").remove();
        $(this).closest('tr').remove();
        updateTotal();
        toastr.error('Barang Dihapus');
    });
    $(document).on('keyup','input[name=antar_jemput]',function(){
        updateTotal();
    });
    $(document).on('keydown','input[name=antar_jemput]','return', function() {
        $('input[name=bayar]').focus();
    });
    $(document).on('keyup','input[name=bayar]',function(){
        var total = parseInt($('#grand_total').attr('value'));
        total = (total)?total:0;
        var bayar = parseInt($(this).val());
        bayar = (bayar)?bayar:0;
        var kembali = bayar-total;
        // if(kembali < 0 ) kembali = 0;
        $('#dibayar').html(formatAngka(bayar));
        $('#kembalian').html(formatAngka(kembali));
    });
    $(document).on('keydown','input[name=bayar]','return', function() {
        $('.btn-bayar').focus();
    });
    $(document).on('click','#btn-remove-antar_jemput',function(){
        $('input[name=antar_jemput]').val('').trigger('keyup');
    });
    $(document).on('click','.btn-bayar',function(){
        var jumlah_baris = $('#keranjang_belanja').find('tbody').find('tr').length;
        var bayar = $('input[name=bayar]').val();
        bayar = (bayar)?bayar:0;
        var total = countTotal();
        var notDone = ($('#form-no-invoice:visible').length>0)||($('#form-agen:visible').length>0)||($('#form-pelanggan:visible').length>0);
        if(notDone){
            $('input[name=bayar]').focus();
            toastr.error('Harap seluruh form diisi dengan benar');
            return false;
        }
        // if(bayar < total){
        //     $('input[name=bayar]').focus();
        //     toastr.error('Harap bayar sesuai jumlah total');
        //     return false;
        // }
        if(jumlah_baris > 0){
            var postData = $('#form_belanja').serialize();
            var konfirmasi = confirm('-------- KONFIRMASI -------\n\nApakah data telah sesuai?');
            if(!konfirmasi)return false;
            var url = $(this).closest('form').attr('action');
            $.ajax({
                method: "POST",
                url: url,
                data: postData,
                success: function(res){
                    if(res.status)
                    {
                        // toastr.success(res.message);
                        document.getElementById('print-invoice').setAttribute("src", res.url);
                        window.frames["print-invoice"].focus();
                        //$('#simpleModal').find('#modal-message').html(res.message);
                        //$('#simpleModal').modal({'show':true,'backdrop':'static','keyboard':false});
                        resetKeranjang();
                    }
                    else
                    {
                        toastr.error(res.message);
                    }
                },
                error: function(){
                    toastr.error('Koneksi Error');
                }
            });
            // $(this).closest('form').submit();
            // toastr.success('Terima Kasih');
        }
        else {
            toastr.error('Tidak Ada Barang Di Keranjang');
        }
        $('input[name=kode_produk]').focus();
    });

    $(document).on('click','.btn-pelanggan-baru',function(){
        $('#form-pelanggan').find('input[name=id_returning],input[name=username_member]').val('');
        $('#form-pelanggan').find('input,textarea').prop('readonly',false);
    });
    $(document).on('click','.btn-modal-pelanggan',function(){
        $('#modalPelanggan').on('shown.bs.modal', function (e) {
            $('#modalPelanggan').find('input[name=nama]').focus();
        });
        $('#modalPelanggan').modal({'show':true,'backdrop':'static','keyboard':false});
    });
    $(document).on('submit','form#form-cari-pelanggan',function(e){
        e.preventDefault();
        var sendData = $(this).serialize();
        var url = $(this).attr('action');
        var boxResult = $(this).closest('.modal-body').find('#hasil-cari-pelanggan');
        boxResult.html('Loading');
        $.ajax({
            method: "POST",
            url: url,
            data: sendData,
            success: function(res){
                if(res.status)
                {
                    toastr.success(res.message);
                    boxResult.html(res.data);
                }
                else
                {
                    boxResult.html('');
                    toastr.error(res.message);
                }
            },
            error: function(){
                toastr.error('Koneksi Error');
            }
        });
    });
    $(document).on('click','.btn-pilih-pelanggan',function(){
        var nama = $(this).closest('tr').find('#pelanggan-nama').html();
        var hp = $(this).closest('tr').find('#pelanggan-hp').html();
        var alamat = $(this).closest('tr').find('#pelanggan-alamat').html();
        var kodepos = $(this).closest('tr').find('#pelanggan-kodepos').html();
        var id_pelanggan = $(this).attr('idpel');
        $('#form-pelanggan').find('input,textarea').val('').prop('readonly',true);
        $('#form-pelanggan').find('input[name=id_returning]').val(id_pelanggan);
        $('#form-pelanggan').find('input[name=nama_pelanggan]').val(nama);
        $('#form-pelanggan').find('input[name=hp_pelanggan]').val(hp);
        $('#form-pelanggan').find('textarea[name=alamat_pelanggan]').val(alamat);
        $('#form-pelanggan').find('input[name=kodepos_pelanggan]').val(kodepos).trigger('keyup');
        $('#modalPelanggan').modal('hide');
    });

    $(document).on('click','.btn-modal-member',function(){
        $('#modalMember').on('shown.bs.modal', function (e) {
            $('#modalMember').find('input[name=nama]').focus();
        });
        $('#modalMember').modal({'show':true,'backdrop':'static','keyboard':false});
    });
    $(document).on('submit','form#form-cari-member',function(e){
        e.preventDefault();
        var sendData = $(this).serialize();
        var url = $(this).attr('action');
        var boxResult = $(this).closest('.modal-body').find('#hasil-cari-member');
        boxResult.html('Loading');
        $.ajax({
            method: "POST",
            url: url,
            data: sendData,
            success: function(res){
                if(res.status)
                {
                    toastr.success(res.message);
                    boxResult.html(res.data);
                }
                else
                {
                    boxResult.html('');
                    toastr.error(res.message);
                }
            },
            error: function(){
                toastr.error('Koneksi Error');
            }
        });
    });
    $(document).on('click','.btn-pilih-member',function(){
        var nama = $(this).closest('tr').find('#member-nama').html();
        var hp = $(this).closest('tr').find('#member-hp').html();
        var alamat = $(this).closest('tr').find('#member-alamat').html();
        var kodepos = $(this).closest('tr').find('#member-kodepos').html();
        var username = $(this).attr('username');
        $('#form-pelanggan').find('input,textarea').val('').prop('readonly',true);
        $('#form-pelanggan').find('input[name=username_member]').val(username);
        $('#form-pelanggan').find('input[name=nama_pelanggan]').val(nama);
        $('#form-pelanggan').find('input[name=hp_pelanggan]').val(hp);
        $('#form-pelanggan').find('textarea[name=alamat_pelanggan]').val(alamat);
        $('#form-pelanggan').find('input[name=kodepos_pelanggan]').val(kodepos).trigger('keyup');
        $('#modalMember').modal('hide');
    });


    $(document).on('click','.btn-modal-produk',function(){
        $('#modalProduk').on('shown.bs.modal', function (e) {
            $('#modalProduk').find('input[name=nama]').focus();
        });
        $('#modalProduk').on('hidden.bs.modal', function (e) {
            $('input[name=kode_produk]').focus();
        });
        $('#modalProduk').modal({'show':true,'backdrop':'static','keyboard':false});
    });
    $(document).on('submit','form#form-cari-produk',function(e){
        e.preventDefault();
        var sendData = $(this).serialize();
        var url = $(this).attr('action');
        var boxResult = $(this).closest('.modal-body').find('#hasil-cari-produk');
        boxResult.html('Loading');
        $.ajax({
            method: "POST",
            url: url,
            data: sendData,
            success: function(res){
                if(res.status)
                {
                    toastr.success(res.message);
                    boxResult.html(res.data);
                }
                else
                {
                    boxResult.html('');
                    toastr.error(res.message);
                }
            },
            error: function(){
                toastr.error('Koneksi Error');
            }
        });
    });
    $(document).on('click','.btn-pilih-produk',function(){
        var idprod = $(this).attr('idprod');
        $('input[name=kode_produk]').val(idprod);
        $('#modalProduk').modal('hide');
    });

    $(document).on('keydown',null,'ctrl+u', function() {
        return false;
    });
    // $(document).on('keydown',null,'F12', function() {
    // 	return false;
    // });
    // $(this).bind("contextmenu", function(e) {
    // 	e.preventDefault();
    // });
})(jQuery);