(function() {
    String.prototype.replaceAll = function(search, replacement) {
        var target = this;
        if(search == '.') search = '\\.';
        return target.replace(new RegExp(search, 'g'), replacement);
    };

    var $selectCustomer = $(".select2-customer");
    var $selectAgent = $(".select2-agent");
    var $selectInvoice = $("#select-invoice");

    var $btnMemberStatus = $('#btn-member-status');
    var $optionNonMember = $('#option-non-member');
    var $optionMember = $('#option-member');

    var $inputSearch = $("#search_item");
    var $tableOrder = $("#table-order");
    var $tableTotal = $("#table-total");
    var $totalItems = $('#total_items');
    var $totalOrders = $('#total_orders');
    var $discount = $('#discount');
    var $shipping = $('#shipping');
    var $note = $('#note');

    var $btnNewCustStat = $('.btn-new-cust-stat');
    var $inputIsMember = $('#input-is-member');

    var $btnAddDiscount = $('#add_discount');
    var $btnSaveDiscount = $('#btn-save-discount');
    var $btnAddShipping = $('#add_shipping');
    var $btnSaveShipping = $('#btn-save-shipping');
    var $btnAddNote = $('#add_note');
    var $btnSaveNote = $('#btn-save-note');
    var $btnSaveNoteItem = $('#btn-save-item-note');
    var $btnModalPayment = $('#btn-modal-payment');
    var $btnModalCustomer = $('#btn-modal-customer');
    var $btnRemoveItem = $('.btn-remove-item');
    var $btnPayNow = $('#btn-pay-now');
    var $btnSaveCustomer = $('#btn-save-customer');
    var $btnPaymentDone = $('#btn-payment-done');
    var $btnPrintCustomer = $('#btn-print-customer');
    var $btnPrintCleanvast = $('#btn-print-cleanvast');
    var $btnUpdateOrder = $('#btn-update-order');

    var $iframePrint = $('#iframePrint');
    
    var $modalFormCustomer = $('#modalFormCustomer');
    var $modalPayment = $('#modalPayment');
    var $modalNoteItem = $('#modalNoteItem');

    var $formCustomer = $('#form-customer');
    var $formOrder = $('#form-order');
    var $formPayment = $('#form-payment');

    var $inputDiscount = $('#input-discount');
    var $inputDiscountModal = $('#input-discount-modal');
    var $inputShipping = $('#input-shipping');
    var $inputShippingModal = $('#input-shipping-modal');
    var $inputNote = $('#input-note');
    var $inputNoteModal = $('#input-note-modal');
    var $inputPaid = $('#input-paid');
    var $selectPaymentMethod = $('#select_payment_method');

    var $tax = $('#tax');
    var $grandTotal = $('#grand-total');
    var $textGrandTotalModal = $('#text-grand-total-modal');
    var $textChange = $('#text-change');
    var $textChangeModal = $('#text-change-modal');

    var $boxPaymentFinish = $('#box-payment-finish');

    var $posItemsWrapper = $('#pos-items');
    var $posItems = $('.pos-item');

    var $searchClear = $('#searchclear');
    var orderItemMaster = '<tr data-id="{productId}"><td><a href="#" data-id="{productId}" class="note-item pull-right">note</a>{productName}</td><td class="text-right" id="row-text-price">{productPriceText}</td><td class="text-right"><input type="number" min="1" name="item[{productId}]" value="{productQty}" data-price="{productPriceRaw}" class="form-control text-center input-qty"></td><td class="text-right" id="subtotal">{subtotal}</td><td><a href="#" class="text-red btn-remove-item"><i class="fa fa-times"></i></a></td></tr>';
    var orderEmpty = '<tr><td colspan="5" class="text-center">Empty</td></tr>';
    
    // ADD CUSTOMER
    $btnNewCustStat.click(function(){
        $('.btn-new-cust-stat').removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        $inputIsMember.val($(this).data('value'));
    });

    $formCustomer.submit(function(e){
        e.preventDefault();
        var postData = $(this).serialize();
        var $form = $(this);
        var $formBox = $form.find('#form-box');
        var $modalContent = $form.find('.modal-body');
        $.ajax({
            cache: false,
            type: "POST",
            timeout: 5000,
            url: "api/add_customer",
            data: postData,
            beforeSend:function(){
                $modalContent.prepend('<div class="alert alert-warning text-center" id="loading">Loading</div>');
            },
            success: function(res)
            {
                $modalContent.find('#loading').remove();
                if(res.status){
                    if($inputIsMember.val() == '1'){
                        $optionMember.trigger('click');
                    } else {
                        $optionNonMember.trigger('click');
                    }
                    $modalFormCustomer.modal('hide');
                    $selectCustomer.html('<option value="'+res.data.id+'">'+res.data.name+'</option>');
                }
            },
            error : function(jqXHR, exception)
            {
                $modalContent.find('#loading').remove();
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'No internet connection.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    });
    // END OF ADD CUSTOMER
    
    // CUSTOMER MEMBER STATUS
    $optionNonMember.click(function(){
        $btnMemberStatus.data('value',0).html($(this).html());
        $selectCustomer.html('');
        changePriceByMemberStatus();
    });
    $optionMember.click(function(){
        $btnMemberStatus.data('value',1).html($(this).html());
        $selectCustomer.html('');
        changePriceByMemberStatus();
    });
    // CUSTOMER MEMBER STATUS

    // SELECT 2 SEARCH CUSTOMER
    function formatOptionCustomer (response) {
        if (response.loading) return "Searching ...";
        var addr = '';
        if(response.member_no) addr += "<br><strong>Reg No:</strong> "+ response.member_no;
        if(response.address) addr += "<br><strong>Address:</strong> "+ response.address;
        return "<div><strong>" + response.name.toUpperCase() +"</strong><br><strong>Phone:</strong> "+ response.phone + addr +"</div>";
    }

    function formatOptionSelectionCustomer (response) {
        if(response.name) {
            var full_text = response.name +" - "+ response.phone;
        }
        return full_text || response.text;
    }

    $selectCustomer.select2({
        placeholder: 'Unknown Customer',
        allowClear: true,
        ajax: {
            url: "api/customers",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    m: $btnMemberStatus.data('value'),
                    p: params.page
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.data,
                    pagination: {
                        more: (params.page * 10) < response.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, 
        minimumInputLength: 1,
        templateResult: formatOptionCustomer, 
        templateSelection: formatOptionSelectionCustomer 
    });
    // END OF SELECT 2 SEARCH CUSTOMER

    // SELECT 2 SEARCH AGENT
    function formatOptionAgent (response) {
        if (response.loading) return "Searching ...";
        return "<div>" + response.name +" - "+ response.phone+"</div>";
    }

    function formatOptionSelectionAgent (response) {
        if(response.name) {
            var full_text = response.name +" - "+ response.phone;
        }
        return full_text || response.text;
    }

    $selectAgent.select2({
        placeholder: 'No Agent',
        allowClear: true,
        ajax: {
            url: "api/agents",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    p: params.page
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.data,
                    pagination: {
                        more: (params.page * 10) < response.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, 
        minimumInputLength: 1,
        templateResult: formatOptionAgent, 
        templateSelection: formatOptionSelectionAgent 
    });
    // END OF SELECT 2 SEARCH AGENT

    // INPUT SEARCH PRODUCT
    $(document).on('keyup','#search_item',function(){
        var keyword = $(this).val();
        if(keyword == '') {
            $posItems.show();
        } else {
            $posItems.hide();
            $posItems.filter(function(index,item){
                var keys = keyword.trim().split(' ');
                var matchOne = 'init';
                var itemName = $(this).find('.item-name').html().toLowerCase();
                console.log(keys.length);
                if(keys.length > 1){
                    $.each(keys,function(index,key){
                        var match = itemName.indexOf(key.toLowerCase()) >= 0 ;
                        if(matchOne === 'init') matchOne = match; 
                        else matchOne = (matchOne && match);
                    });
                    return matchOne;
                } else {
                    return (itemName.indexOf(keyword.trim().toLowerCase()) >= 0);
                }
            }).show();
        }
        if($('.pos-item:visible').length > 0) {
            $posItemsWrapper.find('.alert').remove();
        } else {
            if($posItemsWrapper.find('.alert').length == 0) {
                $posItemsWrapper.append('<div class="alert alert-danger text-center">ITEM NOT FOUND</div>');
            }
        }
    });

    $searchClear.click(function(){
        $(this).prev().val('').trigger('keyup').focus();
    });

    $(document).on('keydown','#search_item','return', function() {
        if($(this).val()==''){
            $(this).blur();
        } else {
            var $visibleItem = $('.pos-item:visible');
            if($visibleItem.length == 1){
                $visibleItem.trigger('click');
                $searchClear.trigger('click');
            }
        }
    });

    $(document).on('keydown','#search_item','esc', function() {
        $searchClear.trigger('click');
    });
    // END OF INPUT SEARCH PRODUCT
    
    // POS ITEM CLICK
    $posItems.click(function(e){
        var id = $(this).data('id');
        var name = $(this).find('.item-name').html();
        var $badge = $(this).find('.badge');
        var price = isMember() ? parseInt($(this).data('price-member')) : parseInt($(this).data('price'));
        var $exist = $tableOrder.find('tbody input[name="item['+id+']"]');
        if($exist && $exist.length) {
            var currentQty = parseInt($exist.val());
            currentQty++;
            $exist.val(currentQty);
            var subtotal = currentQty * price;
            $badge.html(currentQty);
            $exist.closest('tr').find('#subtotal').html(formatAngka(subtotal));
        } else {
            var newItem = orderItemMaster;
            newItem = newItem.replaceAll('{productId}',id);
            newItem = newItem.replaceAll('{productName}',name);
            newItem = newItem.replaceAll('{productPriceRaw}',price);
            newItem = newItem.replaceAll('{productPriceText}',formatAngka(price));
            newItem = newItem.replaceAll('{subtotal}',formatAngka(price));
            newItem = newItem.replaceAll('{productQty}','1');
            $badge.show().html('1');
            if(isNoOrder()){
                $tableOrder.find('tbody').html(newItem);
            } else {
                $tableOrder.find('tbody').append(newItem);
            }
        }
        updateSummary();
    });
    // END OF POS ITEM CLICK

    // DISCOUNT
    $btnSaveDiscount.click(function(){
        var input = $inputDiscountModal.val();
        if(input.endsWith("%") || $.isNumeric(input)){
            if($.isNumeric(input)){
                input = formatAngka(input);
            }
            $discount.html(input);
            updateSummary();
            $('#modalDiscount').modal('hide');
        } else {
            $inputDiscountModal.focus();
        }
    });
    
    $(document).on('keyup','#input-discount-modal','return',function(){
        $btnSaveDiscount.trigger('click');
    });
    
    $(document).on('shown.bs.modal','#modalDiscount',function(){
        $inputDiscountModal.focus();
    });
    // END OF DISCOUNT

    // SHIPPING
    $btnSaveShipping.click(function(){
        var input = $inputShippingModal.val();
        if($.isNumeric(input)){
            input = formatAngka(input);
            $shipping.html(input);
            updateSummary();
            $('#modalShipping').modal('hide');
        } else {
            $inputShippingModal.focus();
        }
    });
    
    $(document).on('keyup','#input-shipping-modal','return',function(){
        $btnSaveShipping.trigger('click');
    });
    
    $(document).on('shown.bs.modal','#modalShipping',function(){
        $inputShippingModal.focus();
    });
    // END OF SHIPPING

    // NOTE
    $btnSaveNote.click(function(){
        var input = $inputNoteModal.val();
        $note.html(input);
        $inputNote.val(input);
        $('#modalNote').modal('hide');
    });
        
    $(document).on('shown.bs.modal','#modalNote',function(){
        $inputNoteModal.focus();
    });
    // END OF NOTE

    // TABLE ORDER
    $(document).on('click','.btn-remove-item',function(e){
        e.preventDefault();
        var id = $(this).closest('tr').data('id');
        $('.pos-item[data-id="'+id+'"]').find('.badge').hide();
        $(this).closest('tr').remove();
        $('.input-note-item-'+id).remove();
        updateSummary();
    });
    $(document).on('change keyup','.input-qty',function(e){
        e.preventDefault();
        var id = $(this).closest('tr').data('id');
        var qty = parseInt($(this).val());
        var price = parseInt($(this).data('price'));
        var subtotal = qty * price;
        if(!subtotal) subtotal = 0;
        $(this).closest('tr').find('#subtotal').html(formatAngka(subtotal));
        $('.pos-item[data-id="'+id+'"]').find('.badge').html(qty);
        updateSummary();
    });
    // END OF TABLE ORDER

    //  PAYMENT
    $(document).on('show.bs.modal','#modalPayment',function(){
        var customer = $selectCustomer.val();
        if(!customer || customer == '' || customer == '0'){
            alert('Please select customer first!');
            $modalPayment.modal('hide');
            return false;
        }
    });
    $(document).on('shown.bs.modal','#modalPayment',function(){
        $inputPaid.focus();
        if(isNoOrder()){
            $btnPayNow.hide();
        } else {
            $btnPayNow.show();
        }
    });

    $(document).on('hidden.bs.modal','#modalPayment',function(){
        $textChange.html('0');
        $textChangeModal.html('0');         
        if($boxPaymentFinish.css('display') != 'none'){
            resetPOS();
        }
    });
    $(document).on('hide.bs.modal','#modalPayment',function(){
        var $modalContent = $modalPayment.find('.modal-body').first();
        $boxPaymentFinish.find('#loading').remove();
    });

    $(document).on('keyup','#input-paid',function(){
        var total = parseInt($textGrandTotalModal.data('value'));
        var paid = parseInt($(this).val());
        var change = paid - total;
        if(!change || change < 0) change = 0;
        $textChange.html(formatAngka(change));
        $textChangeModal.html(formatAngka(change));
    });

    // $(document).on('keyup','#input-paid','return',function(){
    //     if($btnPayNow.is(':visible')){
    //         $btnPayNow.trigger('click');
    //     }
    // });
 
    $formPayment.submit(function(e){
        e.preventDefault();
        $formOrder.submit();
    });

    $formOrder.submit(function(e){
        e.preventDefault();
        var postData = $(this).serialize();
        var paymentData = $formPayment.serialize();
        postData += '&'+paymentData;
        var $modalContent = $modalPayment.find('.modal-body').first();
        
        if(!$selectPaymentMethod.val() || $selectPaymentMethod.val() == '') {
            alert('Please select payment method');
            return false;
        }
        $.ajax({
            cache: false,
            type: "POST",
            timeout: 5000,
            url: "api/add_order",
            data: postData,
            beforeSend:function(){
                $modalContent.prepend('<div class="alert alert-warning text-center" id="loading">Loading</div>');
            },
            success: function(res)
            {
                $modalContent.find('#loading').remove();
                if(res.status){
                    // $modalPayment.modal('hide');
                    $btnPrintCustomer.data('id',res.data.id);
                    $btnPrintCleanvast.data('id',res.data.id);
                    $formPayment.hide();
                    $boxPaymentFinish.show();
                    $boxPaymentFinish.find('.modal-body').prepend('<div class="alert alert-success text-center" id="loading">'+res.message+'</div>');
                } else {
                    alert('Error');
                }
            },
            error: function(jqXHR, exception)
            {
                $modalContent.find('#loading').remove();
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'No internet connection.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    });

    $btnPrintCustomer.click(function(){
        var id = $btnPrintCustomer.data('id');
        var url = $(this).data('base-url') + '/' + id;
        if($iframePrint.attr('src') != url){
            $iframePrint.attr('src',url);
            $iframePrint.one('load', function(){
                $iframePrint.get(0).contentWindow.print();
            });
        } else {
            $iframePrint.get(0).contentWindow.print();
        }
    });

    $btnPrintCleanvast.click(function(){
        var id = $btnPrintCleanvast.data('id');
        var url = $(this).data('base-url') + '/' + id;
        if($iframePrint.attr('src') != url){
            $iframePrint.attr('src',url);
            $iframePrint.one('load', function(){
                $iframePrint.get(0).contentWindow.print();
            });
        } else {
            $iframePrint.get(0).contentWindow.print();
        }
    });

    // END OF PAYMENT

    // FUNCTIONS
    function isMember(){
        return $btnMemberStatus.data('value') == '1' ? true : false;
    }

    function changePriceByMemberStatus(){
        if(isMember()) {
            $('.item-price.member-price').show();
            $('.item-price.non-member-price').hide();
        } else {
            $('.item-price.member-price').hide();
            $('.item-price.non-member-price').show();
        }

        $tableOrder.find('tbody tr').each(function(index,row){
            var id = $(this).data('id');
            if(isMember()) {
                var price = parseInt($('.pos-item[data-id="'+id+'"]').data('price-member'));
            } else {
                var price = parseInt($('.pos-item[data-id="'+id+'"]').data('price'));
            }
            var $inputQty = $(row).find('.input-qty');
            var qty = parseInt($inputQty.val());
            var subtotal = price * qty;

            $(row).find('#row-text-price').html(formatAngka(price));
            $(row).find('.input-qty').data('price',price);
            $(row).find('#subtotal').html(formatAngka(subtotal));
        });
        updateSummary();
    }

    function countItems(){
        return $tableOrder.find('tbody #subtotal').length;
    }

    function countQty(){
        var total = 0;
        $tableOrder.find('tbody input[name^=item]').each(function (index,item) {
            var qty = parseInt($(this).val());
            if(qty) total += qty;
        });
        return total;
    }

    function isNoOrder(){
        if(countItems() > 0) return false;
        return true;
    }

    function getTotalOrders(){
        var total = 0;
        $tableOrder.find('tbody #subtotal').each(function (index, item) {
            var subtotal = parseInt($(this).html().replaceAll('.',''));
            if(subtotal) total += subtotal;
        });
        return total;
    }

    function getDiscount(){
        var total = getTotalOrders();
        var discText = $discount.html().replaceAll('.','');
        var disc = 0;
        if(discText.endsWith('%')){
            disc = parseInt(discText.substring(0, discText.length-1));
            disc = (disc/100) * total;
        } else {
            disc = parseInt(discText);
        }
        return disc;
    }

    function updateSummary(){
        var totalItems = countItems();
        var totalQty = countQty();
        var totalOrders = getTotalOrders();
        var discount = getDiscount();
        var shipping = parseInt($shipping.html().replaceAll('.',''));
        var grandTotal = totalOrders - discount + shipping;
        if(grandTotal<0) grandTotal = 0;
        $inputDiscount.val(discount);
        $inputShipping.val(shipping);
        $totalItems.html(formatAngka(totalItems)+' ('+formatAngka(totalQty)+')');
        $totalOrders.html(formatAngka(totalOrders));
        $grandTotal.html(formatAngka(grandTotal));
        $textGrandTotalModal.data('value',grandTotal).html(formatAngka(grandTotal));
    }

    function resetPOS(){
        $tableOrder.find('tbody').html(orderEmpty);
        $searchClear.trigger('click');
        $inputDiscount.val('');
        $inputPaid.val('');
        $inputDiscountModal.val('');
        $discount.html('0');
        $inputShipping.val('');
        $inputShippingModal.val('');
        $shipping.html('0');
        $inputNote.val('');
        $inputNoteModal.val('');
        $note.html('');
        $selectCustomer.html('');
        $selectAgent.html('');
        $textGrandTotalModal.data('value',0).html(0);
        $textChange.html('0');
        $textChangeModal.html('0');
        $btnPayNow.val('');
        $posItems.find('.badge').html(0).hide();
        $formPayment.show();
        $btnPrintCustomer.data('id','');
        $btnPrintCleanvast.data('id','');
        $iframePrint.attr('src','');
        $boxPaymentFinish.hide();
        $optionNonMember.trigger('click');
        $selectPaymentMethod.val('');
        $('input.datepicker').datepicker("setDate", new Date());
        updateSummary();
        resetItemNote();
    }


    // END OF FUNCTIONS

    // SHORTCUTshortcut
    $(document).bind('keydown', 'ctrl+shift+d', function(e){
        e.preventDefault();
        $btnAddDiscount.trigger('click');
    });

    $(document).bind('keydown', 'ctrl+shift+f', function(e){
        e.preventDefault();
        $inputSearch.focus();
    });

    $(document).bind('keydown', 'ctrl+shift+a', function(e){
        e.preventDefault();
        $selectAgent.select2('open');
    });

    $(document).bind('keydown', 'ctrl+shift+b', function(e){
        e.preventDefault();
        $selectCustomer.select2('open');
    });

    $(document).bind('keydown', 'ctrl+shift+c', function(e){
        e.preventDefault();
        $btnModalCustomer.trigger('click');
    });

    $(document).bind('keydown', 'ctrl+shift+i', function(e){
        e.preventDefault();
        $selectInvoice.select2('open');
    });

    $(document).bind('keydown', 'ctrl+shift+s', function(e){
        e.preventDefault();
        $btnModalPayment.trigger('click');
    });
    // END OF SHORTCUT

    // EDIT ORDER
    $(document).on('click','#btn-update-order',function(e){
        e.preventDefault();
        var postData = $('#input-detail-order-note,#input-detail-order-date').serialize();
        var id = $(this).data('id');
        var $col = $(this).closest('td');
        $.ajax({
            cache: false,
            type: "POST",
            timeout: 5000,
            url: "api/update_order/"+id,
            data: postData,
            beforeSend:function(){
                $col.append('<span id="update-loading">Loading</div>');
            },
            success: function(res)
            {
                $col.find('#update-loading').remove();
                alert(res.message);
            },
            error: function(jqXHR, exception)
            {
                $col.find('#update-loading').remove();
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'No internet connection.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    });
    // END OF EDIT ORDER NOTE

    // ITEM NOTE
    $(document).on('click','.note-item',function(){
        var product_id = $(this).data('id');
        $btnSaveNoteItem.data('id',product_id);
        var qty = $(this).closest('tr').find('.input-qty').val();
        var modalBody = '';
        var $exists = $('.input-note-item-'+product_id);
        var arrExist = [];
        if($exists) {
            $.each($exists,function(index,item){
                arrExist.push($(this).val());
            });
            $exists.remove();
        }
        for(i=0;i<qty;i++){
            var no = i+1;
            var value = (arrExist[i]) ? arrExist[i] : '';
            modalBody += '<textarea type="text" class="form-control textarea-item-note" placeholder="write item '+no+' note here">'+value+'</textarea><br>';
        }
        $modalNoteItem.find('.modal-body').html(modalBody);
        $modalNoteItem.modal({backdrop:'static'});
    });

    $btnSaveNoteItem.click(function(){
        var product_id = $(this).data('id');
        console.log(product_id);
        var $textarea = $(this).closest('.modal-content').find('textarea');
        var inputs = '';
        $.each($textarea,function(index,object){
            var value = $(this).val();
            inputs += '<input type="hidden" name="item_note['+product_id+'][]" class="input-note-item-'+product_id+'" value="'+value+'">';
        });
        $formOrder.append(inputs);
        $modalNoteItem.modal('hide');
    });

    $(document).on('hidden.bs.modal','#modalNoteItem',function(){
        $btnSaveNoteItem.data('id','');
        $(this).find('.modal-body').html('');
    });

    function resetItemNote()
    {
        $('input[class^="input-note-item-"]').remove();
        $btnSaveNoteItem.data('id','');
        $('#modalNoteItem').find('.modal-body').html('');
    }
    // END OF ITEM NOTE
})();