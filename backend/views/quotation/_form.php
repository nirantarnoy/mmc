<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Quotation $model */
/** @var yii\widgets\ActiveForm $form */

$unit_data = \backend\models\Unit::find()->select(['id','name'])->where(['status'=>1])->all();
?>

<div class="quotation-form">

    <?php $form = ActiveForm::begin(['id' => 'form-quotation','options'=>['enctype'=>'multipart/form-data']]); ?>
    <input type="hidden" name="removelist" class="remove-list" value="">
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'quotation_no')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <div class="col-lg-3">
            <?php $model->quotation_date = $model->isNewRecord ? date('d-m-Y') : date('d-m-Y', strtotime($model->quotation_date)); ?>
            <?= $form->field($model, 'quotation_date')->widget(\kartik\date\DatePicker::className(), [
                'type' => \kartik\date\DatePicker::TYPE_COMPONENT_PREPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'customer_id')->widget(\kartik\select2\Select2::className(), [
                'data' => ArrayHelper::map(common\models\Customer::find()->all(), 'id', 'first_name'),
                'options' => ['placeholder' => '--เลือกลูกค้า--','onchange'=>'getAttn($(this))'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'attn_id')->widget(\kartik\select2\Select2::className(), [
                'data' => ArrayHelper::map(\common\models\ContactInfo::find()->all(), 'id', function($data){
                    return $data->dept_name.' '.$data->contact_name;
                }),
                'pluginOptions' => ['allowClear' => true],
                'options' => [
                    'placeholder' => '--เลือก--',
                    'id'=>'select-attn-id'
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'from')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <label for="">สถานะ</label>
            <input type="text" class="form-control" readonly value="">
            <?= $form->field($model, 'status')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3"><?= $form->field($model, 'discount_per')->textInput(['maxlength' => true,'class'=>'form-control discount-per','value'=>$model->discount_per == null?0:$model->discount_per]) ?></div>
        <div class="col-lg-3"><?= $form->field($model, 'discount_amt')->textInput(['maxlength' => true,'class'=>'form-control discount-amt','readonly'=>'readonly','value'=>$model->isNewRecord ?0:$model->discount_amt]) ?></div>
        <div class="col-lg-3"></div>
        <div class="col-lg-3"></div>
    </div>
    <br/>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-striped" id="table-list">
                <thead>
                <tr>
                    <th style="width: 5%;text-align: center;">#</th>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>Size</th>
                    <th>Mat</th>
                    <th style="text-align: right;">จำนวน</th>
                    <th style="text-align: center;">หน่วยนับ</th>
                    <th style="text-align: right;width:10%">ราคา</th>
                    <th style="text-align: right;">รวม</th>
                    <th style="width: 8%"></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($model->isNewRecord): ?>
                    <tr>
                        <td style="text-align: center;">
                            <input type="text" class="form-control" readonly>
                        </td>
                        <td>
                            <input type="hidden" class="line-rec-id" name="line_recid[]"
                                   value="0">
                            <input type="hidden" class="line-product-id" name="line_product_id[]" value="">
                            <input type="text" class="form-control line-product-code" name="line_product_code[]"
                                   value="" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control line-product-name" name="line_product_name[]"
                                   value="">
                        </td>
                        <td>
                            <input type="text" class="form-control line-product-size" name="line_product_size[]"
                                   value="">
                        </td>
                        <td>
                            <input type="text" class="form-control line-product-mat" name="line_product_mat[]"
                                   value="">
                        </td>
                        <td style="text-align: right;">
                            <input type="number" class="form-control line-qty" name="line_qty[]" min="0"
                                   onchange="linecal($(this))">
                        </td>
                        <td style="text-align: center;">
<!--                            <input type="hidden" class="line-product-unit-id" value="" name="line_unit_id[]">-->
<!--                            <input type="text" class="form-control line-product-unit-name" name="line_unit[]" value=""-->
<!--                                   readonly>-->
                            <select name="line_unit_id[]" id="" class="form-control line-product-unit-id">
                                <?php foreach ($unit_data as $valuex):?>
                                    <option value="<?=$valuex->id?>"><?=$valuex->name?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td style="text-align: right">
                            <input type="text" class="form-control line-price" name="line_price[]" value=""
                                   onchange="linecal($(this))">
                        </td>
                        <td style="text-align: right">
                            <input type="text" style="text-align: right;" class="form-control line-total"
                                   name="line_total[]" value="" readonly>
                        </td>
                        <td style="text-align: center;">
<!--                            <input type="file" style="display: none;" class="line-photo" id="1" name="line_photo[]" value="">-->
<!--                            <input type="hidden" class="line-photo-index" name="line_photo_index[]" value="">-->
<!--                            <div class="btn btn-sm btn-default" onclick="showphoto($(this))"><i class="fas fa-file-upload text-danger"></i></div>-->
                            <div class="btn btn-sm btn-danger" onclick="removeline($(this))">ลบ</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php if ($model_line != null): ?>
                        <?php foreach ($model_line as $value): ?>
                            <tr data-var="<?= $value->id ?>">
                                <td style="text-align: center;">
                                    <input type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="hidden" class="line-rec-id" name="line_recid[]"
                                           value="<?= $value->id ?>">
                                    <input type="hidden" class="line-product-id" name="line_product_id[]"
                                           value="<?= $value->product_id ?>">
                                    <input type="text" class="form-control line-product-code" name="line_product_code[]"
                                           value="<?= \backend\models\Product::findCode($value->product_id) ?>"
                                           readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control line-product-name" name="line_product_name[]"
                                           value="<?= $value->product_name != '' ? $value->product_name: \backend\models\Product::findName($value->product_id) ?>" <?=check_is_drummy($value->product_id) == 1 ?'':'readonly'?>>
                                </td>
                                <td>
                                    <input type="text" class="form-control line-product-size" name="line_product_size[]"
                                           value="<?=$value->size_desc?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control line-product-mat" name="line_product_mat[]"
                                           value="<?=$value->mat_desc?>">
                                </td>
                                <td style="text-align: right;">
                                    <input type="number" class="form-control line-qty" name="line_qty[]" min="0"
                                           onchange="linecal($(this))" value="<?= $value->qty ?>">
                                </td>
                                <td style="text-align: center;">
<!--                                    <input type="hidden" class="line-product-unit-id" value="--><?php //= $value->unit_id ?><!--"-->
<!--                                           name="line_unit_id[]">-->
<!--                                    <input type="text" class="form-control line-product-unit-name" name="line_unit[]"-->
<!--                                           value="--><?php //= \backend\models\Unit::findName($value->unit_id) ?><!--" readonly>-->
                                    <select name="line_unit_id[]" id="" class="form-control line-product-unit-id">
                                        <?php foreach ($unit_data as $valuex):?>
                                        <?php
                                            $selected = '';
                                            if($value->unit_id == $valuex->id){
                                                $selected = 'selected';
                                            }
                                            ?>
                                            <option value="<?=$valuex->id?>" <?=$selected?>><?=$valuex->name?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                                <td style="text-align: right">
                                    <input type="text" class="form-control line-price" name="line_price[]"
                                           value="<?= $value->line_price?>" onchange="linecal($(this))">
                                </td>
                                <td style="text-align: right">
                                    <input type="text" style="text-align: right;" class="form-control line-total"
                                           name="line_total[]" value="<?= number_format($value->line_total,2) ?>" readonly>
                                </td>
                                <td style="text-align: center;">
<!--                                    <input type="file" style="display: none;" class="line-photo" name="line_photo[]">-->
<!--                                    <input type="hidden" class="line-photo" value="--><?php //=$value->photo?><!--">-->
<!--                                    --><?php //if($value->photo!=null || $value->photo!=''):?>
<!--                                        <a class="btn btn-sm btn-default" target="_blank" href="--><?php //= \Yii::$app->getUrlManager()->baseUrl . '/uploads/quotation_photo/' . $value->photo ?><!--"><i class="fas fa-file-upload text-success"></i></a>-->
<!--                                    --><?php //else:?>
<!--                                        <div class="btn btn-sm btn-default" onclick="showphoto($(this))"><i class="fas fa-file-upload text-danger"></i></div>-->
<!--                                    --><?php //endif;?>

                                    <div class="btn btn-sm btn-danger" onclick="removeline($(this))">ลบ</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="hidden" class="line-rec-id" name="line_recid[]"
                                       value="0">
                                <input type="hidden" class="line-product-id" name="line_product_id[]" value="">
                                <input type="text" class="form-control line-product-code" name="line_product_code[]"
                                       value="" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control line-product-name" name="line_product_name[]"
                                       value="">
                            </td>
                            <td>
                                <input type="text" class="form-control line-product-size" name="line_product_size[]"
                                       value="">
                            </td>
                            <td>
                                <input type="text" class="form-control line-product-mat" name="line_product_mat[]"
                                       value="">
                            </td>
                            <td style="text-align: right;">
                                <input type="number" class="form-control line-qty" name="line_qty[]" min="0"
                                       onchange="linecal($(this))">
                            </td>
                            <td style="text-align: center;">
<!--                                <input type="hidden" class="line-product-unit-id" value="" name="line_unit_id[]">-->
<!--                                <input type="text" class="form-control line-product-unit-name" name="line_unit[]"-->
<!--                                       value="" readonly>-->
                                <select name="line_unit_id[]" id="" class="form-control line-product-unit-id">
                                    <?php foreach ($unit_data as $valuex):?>
                                        <option value="<?=$valuex->id?>"><?=$valuex->name?></option>
                                    <?php endforeach;?>
                                </select>
                            </td>
                            <td style="text-align: right">
                                <input type="text" class="form-control line-price" name="line_price[]" value=""
                                       onchange="linecal($(this))">
                            </td>
                            <td style="text-align: right">
                                <input type="text" style="text-align: right;" class="form-control line-total"
                                       name="line_total[]" value="" readonly>
                            </td>
                            <td style="text-align: center;">
<!--                                <div class="btn btn-sm btn-default"><i class="fas fa-file-upload text-danger"></i></div>-->
                                <div class="btn btn-sm btn-danger" onclick="removeline($(this))">ลบ</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                </tbody>
                <tfoot>
                <tr>
                    <td>
                        <div class="btn btn-sm btn-primary" onclick="finditem();"><i class="fa fa-plus"></i></div>
                    </td>
                    <td colspan="7">
                        
                    </td>
                    <td><input type="text" class="form-control all-total" style="text-align: right" readonly value="0"></td>
                    <td></td>
                </tr>
                </tfoot>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <!--                --><?php //= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?php if (check_has_order($model->id) == 0): ?>
                    <div class="btn btn-success" onclick="submitForm()">Save</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6" style="text-align: right">
            <?php if (!$model->isNewRecord): ?>
                <a class="btn btn-secondary"
                   href="index.php?r=quotation/print&id=<?= $model->id; ?>">พิมพ์ใบเสนอราคา</a>
                <?php if (check_has_order($model->id) == 0): ?>
                    <a class="btn btn-warning" href="index.php?r=quotation/converttoso&id=<?= $model->id; ?>">สร้างใบสั่งซื้อลูกค้า</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>

<div id="findModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3>รายการสินค้า</h3>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <input type="hidden" name="line_qc_product" class="line_qc_product" value="">
                <table class="table table-bordered table-striped table-find-list" width="100%">
                    <thead>
                    <tr>
                        <th style="width:10%;text-align: center">เลือก</th>
                        <th style="width: 20%;text-align: center">รหัส</th>
                        <th style="width: 20%;text-align: center">รายละเอียด</th>
                        <th>คลังสินค้า</th>
                        <th>คงเหลือ</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-emp-selected" data-dismiss="modalx" disabled><i
                            class="fa fa-check"></i> ตกลง
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-close text-danger"></i> ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>

<?php
function check_has_order($id)
{
    return \backend\models\Order::find()->where(['quotation_id' => $id])->count();
}

function check_is_drummy($product_id){
    $res = 0;
    $model = \backend\models\Product::find()->where(['id' => $product_id])->one();
    if($model){
        $res = $model->is_special;
    }
    return $res;
}

?>

<?php
//$url_to_find_workqueue = \yii\helpers\Url::to(['preinvoice/findworkqueue'], true);
$url_to_find_attn = \yii\helpers\Url::to(['customer/findattn'], true);
$url_to_find_item = \yii\helpers\Url::to(['product/finditem'], true);
$url_to_find_exp_date = \yii\helpers\Url::to(['journalissue/findexpdate'], true);
$js = <<<JS
var selecteditem = [];
var selectedorderlineid = [];
var selecteditemgroup = [];
var customer_id = 0;
var removelist = [];

$(function(){
    calall();
    $(".discount-per").on("change",function(){
       var discount = $(this).val();
       var discount_amt = 0;
       if(discount){
           var total = 0;
           $("table#table-list tbody tr").each(function(){
              total += parseFloat($(this).closest("tr").find(".line-total").val()); 
           });
           if(total > 0){
               discount_amt = (total * discount) /100;
           }
           
           $(".discount-amt").val(parseFloat(discount_amt));
       }
    });
});
function showphoto(e){
    e.closest("tr").find(".line-photo").trigger("click");
}
function submitForm(){
    var check_data = 0;
    $("#table-list tbody tr").each(function(){
          if($(this).closest("tr").find(".line-product-id").val() == '' || $(this).closest("tr").find(".line-total").val() == ''){
            check_data +=1;
          } 
       });
    if(check_data > 0){
         alert('กรุณาเลือกรายละเอียดก่อนทำรายการ');
              return false;
    }else{
        $("#table-list tbody tr").each(function(){
            var photo_selected_file = $(this).closest("tr").find(".line-photo").val();
           $(this).closest("tr").find(".line-photo-index").val(photo_selected_file); 
        });
        $("form#form-quotation").submit();
    }
}
function checkcustomer(e){
  //  alert(e.val());
    if(e.val()!=null){
        customer_id = e.val();
    }
}
function showporec(){
    $("#receiveModal").modal('show');
}
function savereceive(){
    $("form#form-receive").submit();
}
function finditem(){
     //   alert(customer_id);
        $.ajax({
          type: 'post',
          dataType: 'html',
          url:'$url_to_find_item',
          async: false,
          data: {},
          success: function(data){
             // alert(data);
              $(".table-find-list tbody").html(data);
              $("#findModal").modal("show");
          },
          error: function(err){
              //alert(err);
              alert('error na ja');
          }
        });
}

function getAttn(e){
     $.ajax({
          type: 'post',
          dataType: 'html',
          url:'$url_to_find_attn',
          async: false,
          data: {'id': e.val()},
          success: function(data){
            $("#select-attn-id").html(data);
          },
          error: function(err){
              alert(err);
              alert('error na ja');
          }
        });
}

function addline(e){
    var tr = $("#table-list tbody tr:last");
    
                    var clone = tr.clone();
                    //clone.find(":text").val("");
                    // clone.find("td:eq(1)").text("");
                   clone.find(".line-text").val("0");
                   clone.find(".line-order-no").val("");
                   clone.find(".line-qty").val("0");
                   clone.find(".line-price").val("0");
                   clone.find(".line-total").val("0");
                   
                    clone.attr("data-var", "");
                    clone.find('.line-rec-id').val("0");
                    clone.find('.line-photo').val("");
                   
                    tr.after(clone);
    
}
function removeline(e) {
       
                if (confirm("ต้องการลบรายการนี้ใช่หรือไม่?")) {
                if (e.parent().parent().attr("data-var") != '') {
                    removelist.push(e.parent().parent().attr("data-var"));
                    $(".remove-list").val(removelist);
                }
                // alert(removelist);
                // alert(e.parent().parent().attr("data-var"));
    
                if ($("#table-list tbody tr").length == 1) {
                    $("#table-list tbody tr").each(function () {
                        $(this).find(":text").val("");
                       // $(this).find(".line-prod-photo").attr('src', '');
                        $(this).find(".line-item-qty").val('');
                        $(this).find(".line-item-price").val('');
                        $(this).find(".line-item-total").val('');
                        $(this).find(".line-qty").val('');
                        $(this).find(".line-photo").val("");
                        // cal_num();
                    });
                } else {
                    e.parent().parent().remove();
                }
                // cal_linenum();
                // cal_all();
                calall();
            }
        
        
}
function cancelline(e) {
       
                if (confirm("ต้องการยกเลิกรายการนี้ใช่หรือไม่?")) {
                if (e.parent().parent().attr("data-var") != '') {
                    removelist.push(e.parent().parent().attr("data-var"));
                    $(".remove-list").val(removelist);
                }
                if(e.hasClass('btn-secondary')){
                    e.removeClass('btn-secondary');
                    e.addClass('btn-success');
                }else{
                    e.addClass('btn-secondary');
                    e.removeClass('btn-success');
                }
            }
        
        
}

function addselecteditem(e) {
        var id = e.attr('data-var');
        var item_id = e.closest('tr').find('.line-find-item-id').val();
      
        ///// add new 
         var item_code = e.closest('tr').find('.line-find-item-code').val();
         var item_name = e.closest('tr').find('.line-find-item-name').val();
         var onhand = e.closest('tr').find('.line-find-onhand-qty').val();
         // var warehouse_id = e.closest('tr').find('.line-find-warehouse-id').val();
         // var warehouse_name = e.closest('tr').find('.line-find-warehouse-name').val();
         var price = e.closest('tr').find('.line-find-price').val();
         var unit_id = e.closest('tr').find('.line-find-unit-id').val();
         var unit_name = e.closest('tr').find('.line-find-unit-name').val();
         var is_drummy = e.closest('tr').find('.line-find-is-drummy').val();
        ///////
        if (id) {
            if (checkhas(item_id, is_drummy)){
                alert("รหัสสินค้าซ้ำ");
                return false;
            }
            if (e.hasClass('btn-outline-success')) {
                var obj = {};
                obj['id'] = id;
                obj['item_id'] = item_id;
                obj['item_code'] = item_code;
                obj['item_name'] = item_name;
                obj['qty'] = onhand;
                // obj['warehouse_id'] = warehouse_id;
                // obj['warehouse_name'] = warehouse_name;
                obj['price'] = price;
                obj['unit_id'] = unit_id;
                obj['unit_name'] = unit_name;
                obj['is_drummy'] = is_drummy;
                
                selecteditem.push(obj);
                selectedorderlineid.push(obj['id']);
                    // var obj_after = {};
                    // obj_after['qty'] = order_line_qty;
                    // obj_after['price'] = order_line_price;
                    // obj_after['discount'] = 0;
                    // obj_after['total'] = (order_line_qty * order_line_price);
                    //
                    // alert(obj_after['product_group_id']);
                    // alert(obj_after['product_group_name']);
                    // alert(obj_after['qty']);
                    
            
                e.removeClass('btn-outline-success');
                e.addClass('btn-success');
                disableselectitem();
                console.log(selecteditem);
            } else {
                //selecteditem.pop(id);
                $.each(selecteditem, function (i, el) {
                    if (this.id == id) {
                        var qty = this.qty;
                        selecteditem.splice(i, 1);
                        selectedorderlineid.splice(i,1);
                      //  deleteorderlineselected(product_group_id, qty); // update data in selected list
                        console.log(selecteditemgroup);
                      //  caltablecontent(); // refresh table below
                    }
                });
                e.removeClass('btn-success');
                e.addClass('btn-outline-success');
                
                disableselectitem();
                console.log(selecteditem);
                console.log(selectedorderlineid);
                console.log(selecteditemgroup);
            }
        }
        $(".orderline-id-list").val(selectedorderlineid);
}

function checkhas(item_id , is_drummy){
    var has = 0;
    $("#table-list tbody tr").each(function () {
       var id = $(this).closest("tr").find(".line-product-id").val();
       if (id == item_id && is_drummy != 1){
           has = 1;
       }
    });
    return has;
}

function disableselectitem() {
        if (selecteditem.length > 0) {
            $(".btn-emp-selected").prop("disabled", "");
            $(".btn-emp-selected").removeClass('btn-outline-success');
            $(".btn-emp-selected").addClass('btn-success');
        } else {
            $(".btn-emp-selected").prop("disabled", "disabled");
            $(".btn-emp-selected").removeClass('btn-success');
            $(".btn-emp-selected").addClass('btn-outline-success');
        }
}

$(".btn-emp-selected").click(function () {
        var linenum = 0;
        var line_count = 0;
      
        if(selecteditem.length >0){
             var tr = $("#table-list tbody tr:last");
             var last_line_photo_id = tr.closest("tr").find(".line-photo").attr("id");
    //alert(last_line_photo_id);
             for(var i=0;i<=selecteditem.length-1;i++){
               //  var new_text = selecteditem[i]['line_work_type_name'] + "\\n" + "Order No."+selecteditem[i]['line_order_no'];
                   if (tr.closest("tr").find(".line-product-id").val() == "") {
                  //  alert(line_prod_code);
            
                    tr.closest("tr").find(".line-product-id").val(selecteditem[i]['item_id']);
                    tr.closest("tr").find(".line-product-code").val(selecteditem[i]['item_code']);
                    tr.closest("tr").find(".line-product-name").val(selecteditem[i]['item_name']);
                    tr.closest("tr").find(".line-qty").val(0);
                    // tr.closest("tr").find(".line-product-warehouse-id").val(selecteditem[i]['warehouse_id']);
                    // tr.closest("tr").find(".line-product-warehouse-name").val(selecteditem[i]['warehouse_name']);
                    tr.closest("tr").find(".line-price").val(selecteditem[i]['price']);
                    tr.closest("tr").find(".line-product-unit-id").val(selecteditem[i]['unit_id']);
                    tr.closest("tr").find(".line-product-unit-name").val(selecteditem[i]['unit_name']);
                    tr.closest("tr").find(".line-photo").val("");
                    
                    if(selecteditem[i]['is_drummy'] == 1){
                        tr.closest("tr").find(".line-product-name").prop("readonly", "");
                    }else{
                        tr.closest("tr").find(".line-product-name").prop("readonly", "readonly");
                    }
                    //console.log(line_prod_code);
                    } else {
                       
                        var clone = tr.clone();
                        clone.closest("tr").find(".line-rec-id").val('0');
                        clone.closest("tr").find(".line-product-id").val(selecteditem[i]['item_id']);
                        clone.closest("tr").find(".line-product-code").val(selecteditem[i]['item_code']);
                        clone.closest("tr").find(".line-product-name").val(selecteditem[i]['item_name']);
                        clone.closest("tr").find(".line-qty").val(0);
                        // clone.closest("tr").find(".line-product-warehouse-id").val(selecteditem[i]['warehouse_id']);
                        // clone.closest("tr").find(".line-product-warehouse-name").val(selecteditem[i]['warehouse_name']);
                        clone.closest("tr").find(".line-price").val(selecteditem[i]['price']);
                        clone.closest("tr").find(".line-product-unit-id").val(selecteditem[i]['unit_id']);
                        clone.closest("tr").find(".line-product-unit-name").val(selecteditem[i]['unit_name']);
                        clone.closest("tr").find(".line-photo").val("");
                        clone.closest("tr").find(".line-photo").attr("id",(parseInt(last_line_photo_id) +1));
                        
                        if(selecteditem[i]['is_drummy'] == 1){
                            clone.closest("tr").find(".line-product-name").prop("readonly", "");
                        }else{
                            clone.closest("tr").find(".line-product-name").prop("readonly", "readonly");
                        }
                        tr.after(clone);
                    } 
             }
                
          
        }
        
        $("#table-list tbody tr").each(function () {
           linenum += 1;
            $(this).closest("tr").find("td:eq(0)").text(linenum);
            // $(this).closest("tr").find(".line-prod-code").val(line_prod_code);
        });
        
        selecteditem = [];
        selectedorderlineid = [];
        selecteditemgroup = [];

        $("#table-find-list tbody tr").each(function () {
            $(this).closest("tr").find(".btn-line-select").removeClass('btn-success');
            $(this).closest("tr").find(".btn-line-select").addClass('btn-outline-success');
        });
        
        $(".btn-emp-selected").removeClass('btn-success');
        $(".btn-emp-selected").addClass('btn-outline-success');
        $("#findModal").modal('hide'); 
});

function calinvoice(e){
    var line_qty = e.closest('tr').find(".line-qty").val();
    var line_price = e.closest('tr').find(".line-price").val();
    var line_total = parseFloat(line_qty) * parseFloat(line_price);
    e.closest('tr').find('.line-total').val(parseFloat(line_total).toFixed(2));
    
    
    var total_amt = 0;
    var total_all_amt = 0;
    var final_amt = 0;
    var vat_amt = 0;
    
      $("#table-list tbody tr").each(function () {
           var line_amt = $(this).find('.line-total').val();
           if(line_amt != null){
               total_amt = parseFloat(total_amt) + parseFloat(line_amt);
              
           }
      });
    vat_amt = (total_amt * 1) / 100;
    final_amt = parseFloat(total_amt) - vat_amt;
    $("#customerinvoice-total_amount").val(parseFloat(total_amt).toFixed(2));
    $("#customerinvoice-total_all_amount").val(parseFloat(total_amt).toFixed(2));
    $("#customerinvoice-vat_amount").val(parseFloat(vat_amt).toFixed(2));
    $("#customerinvoice-final_amount").val(parseFloat(final_amt).toFixed(2));
}
function linecal(e){
   var qty = e.closest("tr").find(".line-qty").val();
   var price = e.closest("tr").find(".line-price").val();
   e.closest("tr").find(".line-total").val(parseFloat(parseFloat(qty).toFixed(2) * parseFloat(price).toFixed(2)).toFixed(2));
   calall();
}
function calall(){
    
    var total_qty = 0;
    var total_amount = 0;
    var discount_per = $(".discount-per").val();
    var total_all_amount = 0;
    var discount_amt = 0;
    
    if(discount_per == null){
        discount_per = 0;
    }
    
    //alert(discount_per);
  
      $("#table-list tbody tr").each(function () {
           var line_qty = $(this).find('.line-qty').val();
           var line_price = $(this).find('.line-price').val();
           var line_amount = $(this).find('.line-total').val();
          // alert(line_amount);
           if(line_qty != null){
               total_qty = parseFloat(total_qty) + parseFloat(line_qty);
           }
           
           total_amount = parseFloat(total_amount)+ parseFloat(line_qty * line_price);
          
      });
      
      var total_after_discount = (parseFloat(total_amount) - (parseFloat(total_amount) * parseFloat(discount_per) /100 ));
      total_all_amount =  parseFloat(total_after_discount) ; //+ ((parseFloat(total_after_discount) * 7)/100);
      discount_amt = (parseFloat(total_amount) * parseFloat(discount_per) /100 )
      
      
    $(".qty-all-total").val(parseFloat(total_qty).toFixed(2));
    $(".all-total").val(parseFloat(total_amount).toFixed(2));
    $(".discount-amt").val(parseFloat(discount_amt).toFixed(2));
   
}


function printdoc(el)
      {
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
     }
function pullstocksum(e){
    var id = e.val();
    var product_id = e.closest("tr").find(".line-product-id").val();
    if(id > 0 && product_id > 0){
       
        $.ajax({
            type: "POST",
            dataType: "html",
            url: "$url_to_find_exp_date",
            data: {
                'warehouse_id': id,
                'product_id': product_id
            },
            success: function (data) {
                e.closest("tr").find(".line-product-expiry-date").html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        })
    }
}
function settuponhandqty(e){
    var selected = e.find('option:selected');
    var qty = selected.attr('data-foo');
    if(qty > 0){
        e.closest("tr").find(".line-product-onhand").val(qty);
    }
    
}
JS;
$this->registerJs($js, static::POS_END);
?>
