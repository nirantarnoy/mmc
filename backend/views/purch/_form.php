<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Purch */
/* @var $form yii\widgets\ActiveForm */

$rec_status = checkReceive($model->id);
?>

<div class="purch-form">

    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden"
           name="removelist"
           class="remove-list"
           value="">
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'purch_no')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <div class="col-lg-3">
            <?php $model->purch_date = $model->isNewRecord ? date('d/m/Y') : date('d/m/Y', strtotime($model->purch_date)); ?>
            <?= $form->field($model, 'purch_date')->widget(\kartik\date\DatePicker::className(), [
                'value' => date('Y-m-d'),
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'vendor_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Vendor::find()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => 'เลือกผู้ขาย'
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'payment_term_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Paymentterm::find()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => 'เลือกเงื่อนไขชำระเงิน'
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?php $xvalue = $model->isNewRecord ? 'Open' : \backend\helpers\PurchStatus::getTypeById($model->status); ?>
            <?php //echo $form->field($model, 'status')->textInput(['readonly' => 'readonly', 'value' => $xvalue]) ?>
            <label for="">สถานะ</label>
            <input type="text" class="form-control" value="<?= $xvalue ?>" readonly>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">

        </div>
        <div class="col-lg-3"></div>
    </div>

    <br/>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-striped"
                   id="table-list">
                <thead>
                <tr>
                    <th style="width: 5%;text-align: center">
                        #
                    </th>
                    <th>
                        รหัสสินค้า
                    </th>
                    <th>
                        ชื่อสินค้า
                    </th>
                    <th style="text-align: right">
                        จำนวน
                    </th>
                    <th style="text-align: right">
                        ราคา
                    </th>
                    <th style="text-align: right">
                        รวม
                    </th>
                    <th style="text-align: center">
                        -
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if ($model->isNewRecord): ?>
                    <tr>
                        <td style="width: 5%;text-align: center">
                            #
                        </td>
                        <td>
                            <input type="hidden"
                                   class="form-control line-prod-id"
                                   name="line_prod_id[]"
                                   readonly>
                            <input type="text"
                                   class="form-control line-prod-code"
                                   name="line_prod_code[]"
                                   readonly>
                        </td>
                        <td>
                            <input type="text"
                                   class="form-control line-prod-name"
                                   name="line_prod_name[]"
                                   readonly>
                        </td>
                        <td style="text-align: right">
                            <input type="number"
                                   class="form-control line-qty"
                                   name="line_qty[]"
                                   min="0"
                                   onchange="line_cal_amount($(this))">
                        </td>
                        <td style="text-align: right">
                            <input type="text"
                                   class="form-control line-price"
                                   name="line_price[]"
                                   onchange="line_cal_amount($(this))">
                        </td>
                        <td style="text-align: right">
                            <input type="text"
                                   class="form-control line-total"
                                   name="line_total[]"
                                   style="text-align: right"
                                   readonly>
                        </td>
                        <td style="text-align: center">
                            <div class="btn btn-danger btn-sm" onclick="removeline($(this))">
                                <i class="fa fa-trash"></i>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php if (count($model_line)): ?>
                        <?php $i = 0; ?>
                        <?php foreach ($model_line as $value): ?>
                            <?php $i += 1; ?>
                            <tr data-var="<?= $value->id ?>">
                                <td style="width: 5%;text-align: center">
                                    <?= $i ?>
                                </td>
                                <td>
                                    <input type="hidden"
                                           class="form-control line-prod-id"
                                           name="line_prod_id[]"
                                           readonly>
                                    <input type="text"
                                           class="form-control line-prod-code"
                                           name="line_prod_code[]"
                                           readonly
                                           value="<?= \backend\models\Product::findCode($value->product_id) ?>">
                                </td>
                                <td>
                                    <input type="text"
                                           class="form-control line-prod-name"
                                           name="line_prod_name[]"
                                           readonly
                                           value="<?= \backend\models\Product::findName($value->product_id) ?>">
                                </td>
                                <td style="text-align: right">
                                    <input type="number"
                                           style="text-align: right;"
                                           class="form-control line-qty"
                                           name="line_qty[]"
                                           min="0"
                                           value="<?= $value->qty ?>"
                                           onchange="line_cal_amount($(this))">
                                </td>
                                <td style="text-align: right">
                                    <input type="text"
                                           style="text-align: right"
                                           class="form-control line-price"
                                           name="line_price[]"
                                           value="<?= $value->price ?>"
                                           onchange="line_cal_amount($(this))">
                                </td>
                                <td style="text-align: right">
                                    <input type="text"
                                           class="form-control line-total"
                                           name="line_total[]"
                                           style="text-align: right"
                                           value="<?= $value->line_total ?>"
                                           readonly>
                                </td>
                                <td style="text-align: center">
                                    <div class="btn btn-danger btn-sm"
                                         onclick="removeline($(this))">
                                        <i class="fa fa-trash"></i>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td style="width: 5%;text-align: center">

                            </td>
                            <td>
                                <input type="hidden"
                                       class="form-control line-prod-id"
                                       name="line_prod_id[]"
                                       readonly>
                                <input type="text"
                                       class="form-control line-prod-code"
                                       name="line_prod_code[]"
                                       readonly>
                            </td>
                            <td>
                                <input type="text"
                                       class="form-control line-prod-name"
                                       name="line_prod_name[]"
                                       readonly>
                            </td>
                            <td style="text-align: right">
                                <input type="number"
                                       class="form-control line-qty"
                                       name="line_qty[]"
                                       min="0"
                                       onchange="line_cal_amount($(this))">
                            </td>
                            <td style="text-align: right">
                                <input type="text"
                                       class="form-control line-price"
                                       name="line_price[]"
                                       onchange="line_cal_amount($(this))">
                            </td>
                            <td style="text-align: right">
                                <input type="text"
                                       class="form-control line-total"
                                       name="line_total[]"
                                       style="text-align: right"
                                       readonly>
                            </td>
                            <td style="text-align: center">
                                <div class="btn btn-danger btn-sm" onclick="removeline($(this))">
                                    <i class="fa fa-trash"></i>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        <?php if ($model->isNewRecord || $rec_status == 0): ?>
                            <div class="btn btn-primary"
                                 onclick="showfind($(this))">
                                <i class="fa fa-plus-circle"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?php if ($model->isNewRecord || $rec_status == 0): ?>
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?php if (!$model->isNewRecord && $rec_status == 0): ?>
            <div class="btn btn-warning"
                 data-id="<?= $model->id ?>"
                 onclick="showreceive($(this))">
                รับสินค้า
            </div>

            <a class="btn btn-secondary"
               href="<?= \yii\helpers\Url::to(['purch/printpo', 'purch_id' => $model->id], true) ?>">พิมพ์</a>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div id="findModal"
     class="modal fade"
     role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row"
                     style="width: 100%">
                    <div class="col-lg-11">
                        <div class="input-group">
                            <input type="text"
                                   class="form-control search-item"
                                   placeholder="ค้นหาสินค้า">
                            <span class="input-group-addon">
                                        <button type="submit"
                                                class="btn btn-primary btn-search-submit">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <button type="button"
                                class="close"
                                data-dismiss="modal">
                            &times;
                        </button>
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">

                <input type="hidden"
                       name="line_qc_product"
                       class="line_qc_product"
                       value="">
                <table class="table table-bordered table-striped table-find-list"
                       width="100%">
                    <thead>
                    <tr>
                        <th style="text-align: center">
                            เลือก
                        </th>
                        <th>
                            รหัสสินค้า
                        </th>
                        <th>
                            รายละเอียด
                        </th>
                        <th>หน่วยนับ</th>
                        <th>ยอดคงเหลือ</th>
                        <!--                        <th>จำนวนคงเหลือ</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-product-selected"
                        data-dismiss="modalx"
                        disabled>
                    <i
                            class="fa fa-check"></i>
                    ตกลง
                </button>
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    <i
                            class="fa fa-close text-danger"></i>
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>
<div id="receiveModal"
     class="modal fade"
     role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row"
                     style="width: 100%">
                    <div class="col-lg-11">
                        <h4>
                            รับใบสั่งซื้อ</h4>
                    </div>
                    <div class="col-lg-1">
                        <button type="button"
                                class="close"
                                data-dismiss="modal">
                            &times;
                        </button>
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <form action="<?= \yii\helpers\Url::to(['purch/savereceive'], true) ?>" method="post"
                      id="form-receive">
                    <input type="hidden" class="po-id" name="po_id" value="">
                    <table class="table table-bordered table-striped table-receive-list"
                           width="100%">
                        <thead>
                        <tr>
                            <th>
                                รหัสสินค้า
                            </th>
                            <th>
                                รายละเอียด
                            </th>
                            <th>
                                จำนวนซื้อ
                            </th>
                            <th style="text-align: right">
                                ค้างรับ
                            </th>
                            <th style="text-align: right">
                                รับเข้า
                            </th>
                            <th>
                                ยกเลิก
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-receive"
                        data-dismiss="modalx"
                        onclick="savereceive($(this))">
                    <i
                            class="fa fa-check"></i>
                    ตกลง
                </button>
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    <i
                            class="fa fa-close text-danger"></i>
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>
<?php
function checkReceive($id)
{
    $res = 0;
    if ($id) {
        $po_qty = \common\models\PurchLine::find()->where(['purch_id' => $id])->sum('qty');
        $rec_qty = \common\models\StockTrans::find()->where(['trans_module_id' => 2, 'ref_id' => $id])->sum('qty');
        if ($rec_qty >= $po_qty) {
            $res = 1;
        }
    }
    return $res;
}

?>
<?php
$url_to_find_item = \yii\helpers\Url::to(['product/finditem'], true);
$url_to_receive = \yii\helpers\Url::to(['purch/purchreceive'], true);
$js = <<<JS
  var removelist = [];
  var selecteditem = [];
$(function (){
    $(".btn-search-submit").click(function (){
         var txt = $(".search-item").val();
         $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {txt_search: txt},
              'success': function(data) {
                  //  alert(data);
                   $(".table-find-list tbody").html(data);
                 //  $("#findModal").modal("show");
                 }
         });
      });
});
function showfind(e){
    //alert();
      $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {},
              'success': function(data) {
                  // alert(data);
                   $(".table-find-list tbody").html(data);
                   $("#findModal").modal("show");
                 }
              });
      
  }
  function addselecteditem(e) {
        var id = e.attr('data-var');
        var code = e.closest('tr').find('.line-find-item-code').val();
        var name = e.closest('tr').find('.line-find-item-name').val();
        var price = e.closest('tr').find('.line-find-price').val();
        if (id) {
            if (e.hasClass('btn-outline-success')) {
                var obj = {};
                obj['id'] = id;
                obj['code'] = code;
                obj['name'] = name;
                obj['price'] = price;
                selecteditem.push(obj);
                
                e.removeClass('btn-outline-success');
                e.addClass('btn-success');
                disableselectitem();
                console.log(selecteditem);
            } else {
                //selecteditem.pop(id);
                $.each(selecteditem, function (i, el) {
                    if (this.id == id) {
                        selecteditem.splice(i, 1);
                    }
                });
                e.removeClass('btn-success');
                e.addClass('btn-outline-success');
                disableselectitem();
                console.log(selecteditem);
            }
        }
    }
     function disableselectitem() {
        if (selecteditem.length > 0) {
            $(".btn-product-selected").prop("disabled", "");
            $(".btn-product-selected").removeClass('btn-outline-success');
            $(".btn-product-selected").addClass('btn-success');
        } else {
            $(".btn-product-selected").prop("disabled", "disabled");
            $(".btn-product-selected").removeClass('btn-success');
            $(".btn-product-selected").addClass('btn-outline-success');
        }
    }
    $(".btn-product-selected").click(function () {
        var linenum = 0;
        if (selecteditem.length > 0) {
            for (var i = 0; i <= selecteditem.length - 1; i++) {
                var line_prod_id = selecteditem[i]['id'];
                var line_prod_code = selecteditem[i]['code'];
                var line_prod_name = selecteditem[i]['name'];
                var line_prod_price = selecteditem[i]['price'];
                
                 if(check_dup(line_prod_id) == 1){
                        alert("รายการสินค้า " +line_prod_code+ " มีในรายการแล้ว");
                        return false;
                    }
                
                var tr = $("#table-list tbody tr:last");
                
                if (tr.closest("tr").find(".line-prod-code").val() == "") {
                    tr.closest("tr").find(".line-prod-id").val(line_prod_id);
                    tr.closest("tr").find(".line-prod-code").val(line_prod_code);
                    tr.closest("tr").find(".line-prod-name").val(line_prod_name);
                    tr.closest("tr").find(".line-price").val(line_prod_price);
                     tr.closest("tr").find(".line-qty").val(1);
                     tr.closest("tr").find(".line-total").val(0);

                    //cal_num();
                    console.log(line_prod_code);
                } else {
                   // alert("dd");
                    console.log(line_prod_code);
                    //tr.closest("tr").find(".line_code").css({'border-color': ''});

                    var clone = tr.clone();
                    //clone.find(":text").val("");
                    // clone.find("td:eq(1)").text("");
                    clone.find(".line-prod-id").val(line_prod_id);
                    clone.find(".line-prod-code").val(line_prod_code);
                    clone.find(".line-prod-name").val(line_prod_name);
                    clone.find(".line-price").val(line_prod_price);
                     clone.find(".line-qty").val(1);
                     clone.find(".line-total").val(0);

                    clone.attr("data-var", "");
                    clone.find('.rec-id').val("");

                    clone.find(".line-price").on("keypress", function (event) {
                        $(this).val($(this).val().replace(/[^0-9\.]/g, ""));
                        if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
                            event.preventDefault();
                        }
                    });

                    tr.after(clone);
                    //cal_num();
                }
            }
          cal_num();
        }
        $("#table-list tbody tr").each(function () {
            linenum += 1;
            $(this).closest("tr").find("td:eq(0)").text(linenum);
            // $(this).closest("tr").find(".line-prod-code").val(line_prod_code);
        });
        selecteditem.length = 0;

        $("#table-find-list tbody tr").each(function () {
            $(this).closest("tr").find(".btn-line-select").removeClass('btn-success');
            $(this).closest("tr").find(".btn-line-select").addClass('btn-outline-success');
        });
        $(".btn-product-selected").removeClass('btn-success');
        $(".btn-product-selected").addClass('btn-outline-success');
        $("#findModal").modal('hide');
    });
   
  function cal_num(){
      $("#table-list tbody tr").each(function(){
          var x = $(this).closest('tr').find('.line-prod-id').val();
         // alert(x); 
      });
  }
   function check_dup(prod_id){
      var _has = 0;
      $("#table-list tbody tr").each(function(){
          var p_id = $(this).closest('tr').find('.line-prod-id').val();
         // alert(p_id + " = " + prod_id);
          if(p_id == prod_id){
              _has = 1;
          }
      });
      return _has;
    }
//  function removeline(e) {
//        if (confirm("ต้องการลบรายการนี้ใช่หรือไม่?")) {
//            if (e.parent().parent().attr("data-var") != '') {
//                removelist.push(e.parent().parent().attr("data-var"));
//                $(".remove-list").val(removelist);
//            }
//            // alert(removelist);
//
//            if ($("#table-list tbody tr").length == 1) {
//                $("#table-list tbody tr").each(function () {
//                    $(this).find(":text").val("");
//                   // $(this).find(".line-prod-photo").attr('src', '');
//                    $(this).find(".line-price").val(0);
//                    cal_num();
//                });
//            } else {
//                e.parent().parent().remove();
//            }
//            cal_linenum();
//            cal_all();
//        }
//    }
    function line_cal_amount(e){
      var qty = e.closest('tr').find('.line-qty').val();
      var price = e.closest('tr').find('.line-price').val();
      if(qty != null && price != null){
          var total = (qty * price);
          e.closest('tr').find('.line-total').val(addCommas(parseFloat(total)));
      }
    }
    function cal_linenum() {
        var xline = 0;
        $("#table-list tbody tr").each(function () {
            xline += 1;
            $(this).closest("tr").find("td:eq(0)").text(xline);
        });
    }
    function removeline(e) {
        if (confirm("ต้องการลบรายการนี้ใช่หรือไม่?")) {
            if (e.parent().parent().attr("data-var") != '') {
                removelist.push(e.parent().parent().attr("data-var"));
                $(".remove-list").val(removelist);
            }
            // alert(removelist);

            if ($("#table-list tbody tr").length == 1) {
                $("#table-list tbody tr").each(function () {
                    $(this).find(":text").val("");
                   // $(this).find(".line-prod-photo").attr('src', '');
                    $(this).find(".line-price").val(0);
                    cal_num();
                });
            } else {
                e.parent().parent().remove();
            }
            cal_linenum();
            cal_all();
        }
    }
    
    function showreceive(e){
      var purch_id = e.attr('data-id');
   //   alert(purch_id);
      if(purch_id){
          $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_receive",
              'data': {'purch_id': purch_id},
              'success': function(data) {
                  // alert(data);
                   $(".table-receive-list tbody").html(data);
                   $("#receiveModal").find('.po-id').val(purch_id);
                   $("#receiveModal").modal("show");
                 }
              });
      }
  }
  function savereceive(e){
      $("#form-receive").submit();
  }
    
    function addCommas(nStr) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
 }

JS;
$this->registerJs($js, static::POS_END);
?>