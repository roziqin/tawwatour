(function() {
    $modalViewOrder = $('#modalViewOrder');
    $searchOrderLoading = $('#search-order-loading');
    $formViewOrder = $('#form-view-order');
    $inputfilterInvoice = $('#filter_invoice');
    $inputfilterName = $('#filter_name');
    $inputfilterPhone = $('#filter_phone');
    $viewOrderResult = $('#view-order-result');
    $btnSearchOrder = $('#btn-search-order');

    // VIEW/SEARCH ORDER
    $(document).on('shown.bs.modal','#modalViewOrder',function(){
        $inputfilterInvoice.focus();
    });
    $(document).on('hidden.bs.modal','#modalViewOrder',function(){
        resetModalViewOrder();
    });

    $(document).on('click','.btn-view-order',function(){
        var invoice = $(this).data('invoice');
        var postData = 'invoice='+invoice;
        $.ajax({
            cache: false,
            type: "POST",
            timeout: 5000,
            url: "api/getOrder",
            data: postData,
            beforeSend:function(){
                $searchOrderLoading.show();
                $viewOrderResult.html('');
            },
            success: function(res)
            {
                if(res.status){
                    $viewOrderResult.html(res.view);
                } else {
                    $viewOrderResult.html('<div class="alert alert-danger">'+res.message+'</div>');
                }
            },
            error: function(msg)
            {
                alert('Error');
            },
            complete: function()
            {
                $searchOrderLoading.hide();
            }
        });
    });

    $(document).on('submit','#form-search-order',function(e){
        e.preventDefault();
        var postData = $(this).serialize();
        $.ajax({
            cache: false,
            type: "POST",
            timeout: 5000,
            url: "api/searchOrder",
            data: postData,
            beforeSend:function(){
                $searchOrderLoading.show();
                $viewOrderResult.html('');
            },
            success: function(res)
            {
                if(res.status){
                    $viewOrderResult.html(res.view);
                } else {
                    $viewOrderResult.html('<div class="alert alert-danger">'+res.message+'</div>');
                }
            },
            error: function(msg)
            {
                alert('Error');
            },
            complete: function()
            {
                $searchOrderLoading.hide();
            }
        });
    });

    $(document).on('submit','#form-view-order-payment',function(e){
        e.preventDefault();
        var postData = $(this).serialize();
        $.ajax({
            cache: false,
            type: "POST",
            timeout: 5000,
            url: "api/add_payment",
            data: postData,
            beforeSend:function(){
                $searchOrderLoading.show();
                $viewOrderResult.html('');
            },
            success: function(res)
            {
                if(res.status){
                    $viewOrderResult.html(res.view);
                } else {
                    $viewOrderResult.html('<div class="alert alert-danger">'+res.message+'</div>');
                }
            },
            error: function(msg)
            {
                alert('Error');
            },
            complete: function()
            {
                $searchOrderLoading.hide();
            }
        });
    });

    $(document).on('click','#btn-vo-print-customer',function(){
        var $iframePrint = $(this).closest('.modal').find('#iframePrintView');
        var url = $(this).data('base-url');
        if($iframePrint.attr('src') != url){
            $iframePrint.attr('src',url);
            $iframePrint.one('load', function(){
                $iframePrint.get(0).contentWindow.print();
            });
        } else {
            $iframePrint.get(0).contentWindow.print();
        }
    });

    $(document).on('click','#btn-vo-print-cleanvast',function(){
        var $iframePrint = $(this).closest('.modal').find('#iframePrintView');
        var url = $(this).data('base-url');
        if($iframePrint.attr('src') != url){
            $iframePrint.attr('src',url);
            $iframePrint.one('load', function(){
                $iframePrint.get(0).contentWindow.print();
            });
        } else {
            $iframePrint.get(0).contentWindow.print();
        }
    });

    function resetModalViewOrder()
    {
        $searchOrderLoading.hide();
        $inputfilterInvoice.val('');
        $inputfilterName.val('');
        $inputfilterPhone.val('');
        $viewOrderResult.html('');
    }
    // END OF VIEW/SEARCH ORDER
})();