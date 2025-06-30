<?php

namespace backend\controllers;

use backend\models\OrderSearch;
use backend\models\Quotation;
use backend\models\QuotationSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

date_default_timezone_set('Asia/Bangkok');
/**
 * QuotationController implements the CRUD actions for Quotation model.
 */
class QuotationController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST','GET'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Quotation models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $viewstatus = 1;

        if (\Yii::$app->request->get('viewstatus') != null) {
            $viewstatus = \Yii::$app->request->get('viewstatus');
        }

        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new QuotationSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
//        if($viewstatus ==1){
//            $dataProvider->query->andFilterWhere(['status'=>$viewstatus]);
//        }
//        if($viewstatus == 2){
//            $dataProvider->query->andFilterWhere(['status'=>0]);
//        }

        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
            'viewstatus' => $viewstatus,
        ]);
    }

    /**
     * Displays a single Quotation model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Quotation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Quotation();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $xdate = explode('-', $model->quotation_date);
                $t_date = date('Y-m-d');
                if (count($xdate) > 1) {
                    $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0];
                }

                $line_product_id = \Yii::$app->request->post('line_product_id');
                $line_product_name = \Yii::$app->request->post('line_product_name');
                $line_qty = \Yii::$app->request->post('line_qty');
                $line_price = \Yii::$app->request->post('line_price');
                $line_unit_id = \Yii::$app->request->post('line_unit_id');
                $line_total = \Yii::$app->request->post('line_total');

                $line_product_size = \Yii::$app->request->post('line_product_size');
                $line_product_mat = \Yii::$app->request->post('line_product_mat');

                $line_photo = [];
                $file_data = [];
                $fine_data_name_to_save = [];
//                $line_photo_index = \Yii::$app->request->post('line_photo_index');
//                $uploaded = $_FILES['line_photo'];
//                for($x=0; $x<=count($line_photo_index)-1; $x++) {
//                   // echo $line_photo_index[$x].'<br />';
//                   if($line_photo_index != null || $line_photo_index != '') {
//                       $file_data[$x] = [
//                           'name'=>$uploaded['name'][$x],
//                           'type'=>$uploaded['type'][$x],
//                           'tmp_name'=>$uploaded['tmp_name'][$x],
//                           'error'=>$uploaded['error'][$x],
//                           'size'=>$uploaded['size'][$x],
//                       ];
//                   }else{
//                       $file_data[$x] = '';
//                   }
//                }
//
//                if($file_data != null) {
//                    foreach ($file_data as $key => $value) {
//                        if($value == null || $value == '') {
//                            $fine_data_name_to_save[$key] = '';
//                        }else{
//                           // $file_saveas = 'photo_'.time(). '.' . $value['type'];
//                           // $value->saveAs('uploads/quotation_photo/' . $file_saveas);
//                            $tmpName = $value['tmp_name'];
//                            $fileName = basename($value['name']);
//                            $uploadDir = 'uploads/quotation_photo/';
//                            move_uploaded_file($tmpName, $uploadDir . $fileName);
//                            $fine_data_name_to_save[$key] = $fileName;
//                        }
//                    }
//                }

//                if (!empty($uploaded)) {
//
//                    for($x = 0; $x<= count($uploaded)-1; $x++) {
//                        if(!empty($uploaded[$x])){
//
//                        }
//                    }

//                    foreach ($uploaded as $file) {
//                        $uploaded_file = "photo_" . $file->baseName . '.' . $file->extension;
////                        if ($uploaded->saveAs('uploads/quotation_photo/' . $uploaded_file)) {
////                            array_push($line_photo, $uploaded_file);
////                        }
//                        $file->saveAs('uploads/quotation_photo/' . $uploaded_file);
//                        array_push($line_photo, $uploaded_file);
//
//                    }
          //      }

             //   print_r($file_data);return;

                $model->quotation_no = $model::getLastNo();
                $model->quotation_date = \Yii::$app->formatter->asDate($model->quotation_date, 'php:Y-m-d');;// date('Y-m-d', strtotime($t_date));
                $model->status = 0;
                if ($model->save(false)) {
                    $total_all = 0;
                    if ($line_product_id != null) {
                        for ($i = 0; $i <= count($line_product_id)-1; $i++) {
                            $line_total_new = str_replace(',', '', $line_total[$i]);
                            $model_line = new \common\models\QuotationLine();
                            $model_line->quotation_id = $model->id;
                            $model_line->product_id = $line_product_id[$i];
                            $model_line->qty = $line_qty[$i];
                            $model_line->unit_id = $line_unit_id[$i];
                            $model_line->line_price = $line_price[$i];
                            $model_line->line_total = $line_total_new;
                            $model_line->product_name = $line_product_name[$i];
                            $model_line->size_desc = $line_product_size[$i];
                            $model_line->mat_desc = $line_product_mat[$i];
                         //   $model_line->photo = $fine_data_name_to_save[$i];
                            if ($model_line->save(false)) {
                                $total_all += $model_line->line_total;
                            }
                        }
                    }
                    $vat_amount = (($total_all - $model->discount_amt) * 7) / 100;
                    $model->total_text = $this->numtothai(number_format(($total_all - $model->discount_amt) + $vat_amount,2));
                    $model->save(false);
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Quotation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_detail = \common\models\QuotationLine::find()->where(['quotation_id' => $id])->all();

        if ($this->request->isPost && $model->load($this->request->post())) {
            $xdate = explode('-', $model->quotation_date);
            $t_date = date('Y-m-d');
            if (count($xdate) > 1) {
                $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0];
            }

            $line_product_id = \Yii::$app->request->post('line_product_id');
            $line_product_name = \Yii::$app->request->post('line_product_name');
            $line_qty = \Yii::$app->request->post('line_qty');
            $line_price = \Yii::$app->request->post('line_price');
            $line_unit_id = \Yii::$app->request->post('line_unit_id');
            $line_total = \Yii::$app->request->post('line_total');
            $line_recid = \Yii::$app->request->post('line_recid');
            $removelist = \Yii::$app->request->post('removelist');

            $line_product_size = \Yii::$app->request->post('line_product_size');
            $line_product_mat = \Yii::$app->request->post('line_product_mat');

            $model->quotation_date = \Yii::$app->formatter->asDate($model->quotation_date, 'php:Y-m-d'); ;//date('Y-m-d', strtotime($t_date));
            if ($model->save(false)) {
                $total_all = 0;
                if ($line_product_id != null) {
                    for ($i = 0; $i <= count($line_product_id)-1; $i++) {
                        $line_total_new = str_replace(',', '', $line_total[$i]);
                        if ($line_recid[$i] == 0) {
                            $model_line = new \common\models\QuotationLine();
                            $model_line->quotation_id = $model->id;
                            $model_line->product_id = $line_product_id[$i];
                            $model_line->qty = $line_qty[$i];
                            $model_line->unit_id = $line_unit_id[$i];
                            $model_line->line_price = $line_price[$i];
                            $model_line->line_total = $line_total_new;
                            $model_line->size_desc = $line_product_size[$i];
                            $model_line->mat_desc = $line_product_mat[$i];
                            $model_line->product_name = $line_product_name[$i];
                            if ($model_line->save(false)) {
                                $total_all += $model_line->line_total;
                            }
                        } else {
                            $model_line_update = \common\models\QuotationLine::find()->where(['id' => $line_recid[$i]])->one();
                            $model_line_update->product_id = $line_product_id[$i];
                            $model_line_update->qty = $line_qty[$i];
                            $model_line_update->unit_id = $line_unit_id[$i];
                            $model_line_update->line_price = $line_price[$i];
                            $model_line_update->line_total = $line_total_new;
                            $model_line_update->product_name = $line_product_name[$i];
                            $model_line_update->size_desc = $line_product_size[$i];
                            $model_line_update->mat_desc = $line_product_mat[$i];
                            if ($model_line_update->save(false)) {
                                $total_all += $model_line_update->line_total;
                            }
                        }

                    }
                }
                $vat_amount = (($total_all - $model->discount_amt) * 7) / 100;
               // echo number_format(($total_all  - $model->discount_amt) + $vat_amount,2);return;
                $model->total_text = $this->numtothai(number_format(($total_all  - $model->discount_amt) + $vat_amount,2));
                $model->save(false);


                if ($removelist != null) {
                    $x = explode(',', $removelist);
                    if ($x != null) {
                        for ($i = 0; $i < count($x); $i++) {
                            \common\models\QuotationLine::deleteAll(['id' => $x[$i]]);
                        }
                    }

                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_detail
        ]);
    }

    public function actionPrint($id)
    {
        $model = \common\models\Quotation::findOne($id);
        $model_line = \common\models\QuotationLine::find()->where(['quotation_id' => $id])->all();
        return $this->render('_print', [
            'model' => $model,
            'model_line' => $model_line
        ]);
    }

    /**
     * Deletes an existing Quotation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        \common\models\QuotationLine::deleteAll(['quotation_id' => $id]);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Quotation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Quotation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quotation::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionConverttoso($id)
    {
        $total_all = 0;
        $order_new_id = 0;
        if ($id) {
            $model = \common\models\Quotation::findOne($id);
            $model_line = \common\models\QuotationLine::find()->where(['quotation_id' => $id])->all();
            if ($model) {
                $model_order = new \backend\models\Order();
                $model_order->order_no = $model_order::getLastNo();
                $model_order->order_date = date('Y-m-d');
                $model_order->customer_id = $model->customer_id;
                $model_order->customer_name = $model->customer_name;
                $model_order->status = 0;
                $model_order->quotation_id = $model->id;
                if ($model_order->save(false)) {
                    $order_new_id = $model_order->id;
                    foreach ($model_line as $line) {
                        $model_order_line = new \common\models\OrderLine();
                        $model_order_line->order_id = $model_order->id;
                        $model_order_line->product_id = $line->product_id;
                        $model_order_line->qty = $line->qty;
                        $model_order_line->price = $line->line_price;
                        $model_order_line->line_total = $line->line_total;
                        if ($model_order_line->save(false)) {
                            $model_trans = new \backend\models\Stocktrans();
                            $model_trans->journal_no = $model_trans::getIssueLastNo();
                            $model_trans->trans_date = date('Y-m-d H:i:s');
                            $model_trans->product_id = $line->product_id;
                            $model_trans->qty = $line->qty;
                            $model_trans->created_by = \Yii::$app->user->id;
                            $model_trans->activity_type_id = 3; // 1 = quotation
                            $model_trans->trans_stock_type = 2; //1 =in and 2 = out
                            $model_trans->trans_ref_id = $model_order->id; // ref order id
                            $model_trans->status = 0;
                            $model_trans->stock_type_id = 2;
                            $model_trans->warehouse_id = 1;
                            if ($model_trans->save(false)) {
                                $this->createIssueForOrder($model_order_line->product_id,$line->qty,1,$model_order->id);
                                $this->updateStock($model_order_line->product_id, $line->qty, 1); // update stock onhand
                            }
                            $total_all += $model_order_line->line_total;
                        }
                    }

                    $model_order->total_amount = $total_all;
                    $model_order->save(false);
                }
            }
        }
        if ($total_all) {
            $model->status = 2; // close this quotation
            $model->save(false);
            return $this->redirect(['order/update', 'id' => $order_new_id]);
        }
    }

    public function updateStock($product_id, $qty, $warehouse_id)
    {
        if ($product_id && $qty) {
            $model = \common\models\StockSum::find()->where(['product_id' => $product_id, 'warehouse_id' => $warehouse_id])->one();
            if ($model) {
                $model->qty = ((float)$model->qty) + (float)$qty;
                $model->save(false);
            }
        }
    }

    public function createIssueForOrder($product_id, $qty, $warehouse_id, $ref_id)
    {
        if ($product_id && $qty && $warehouse_id && $ref_id) {
            $model = new \backend\models\Journalissue();
            $model->journal_no = $model::getLastNo();
            $model->trans_date = date('Y-m-d H:i:s');
            $model->issue_for_id = $ref_id;
            $model->status = 1;
            if ($model->save(false)) {
                $model_line = new \common\models\JouranlIssueLine();
                $model_line->journal_issue_id = $model->id;
                $model_line->product_id = $product_id;
                $model_line->qty = $qty;
                $model_line->warehouse_id = $warehouse_id;
                $model_line->status = 0;
                $model_line->save(false);
            }
        }
    }


    public function numtothai($num)
    {
        $return = "";
        $num = str_replace(",", "", $num);
        $number = explode(".", $num);
        if (sizeof($number) > 2) {
            return 'รูปแบบข้อมุลไม่ถูกต้อง';
            exit;
        } else if (sizeof($number) == 1) {
            $number[1] = 0;
        }
        // return $number[0];
        $return .= self::numtothaibathstring($number[0]) . "บาท";

        $stang = intval($number[1]);
        // return $stang;
        if ($stang > 0) {
            if (strlen($stang) == 1) {
                $stang = $stang . '0';
            }
            if ($stang == '10') {
                $return .= 'สิบสตางค์';
            } else if ($stang == '11') {
                $return .= 'สิบเอ็ดสตางค์';
            } else if ($stang == '12') {
                $return .= 'สิบสองสตางค์';
            } else if ($stang == '13') {
                $return .= 'สิบสามสตางค์';
            } else if ($stang == '14') {
                $return .= 'สิบสี่สตางค์';
            } else if ($stang == '15') {
                $return .= 'สิบห้าสตางค์';
            } else if ($stang == '16') {
                $return .= 'สิบหกสตางค์';
            } else if ($stang == '17') {
                $return .= 'สิบเจ็ดสตางค์';
            } else if ($stang == '18') {
                $return .= 'สิบแปดสตางค์';
            } else if ($stang == '19') {
                $return .= 'สิบเก้าสตางค์';
            } else {
                $return .= self::numtothaistring($stang) . "สตางค์";
            }

        } else {
            $return .= "ถ้วน";
        }
        return $return;
    }
    public function numtothaibathstring($num){
        $return_str = '';
        $txtnum1 = array('', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
        $txtnum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
        if(strlen($num)>1){
            $num_arr = str_split($num);
            $count = count($num_arr);
            foreach ($num_arr as $key => $val) {
                if ($count > 1 && $val == 1 && $key == ($count - 1)) {
                    $return_str .= "เอ็ด";
                } else if ($count > 1 && $val == 1 && $key == 2) {
                    $return_str .= $txtnum2[$val];
                } else if ($count > 1 && $val == 2 && $key == ($count - 2)) {
                    $return_str .= "ยี่" . $txtnum2[$count - $key - 1];
                } else if ($count > 1 && $val == 1 && $key == 0) {
                    $return_str.=$txtnum2[$val];
                } else {
                    $return_str .= $txtnum1[$val] . $txtnum2[$count - $key - 1];
                }
            }
        }else{
            $return_str = $txtnum1[intval($num)];
        }
        return $return_str;
    }
    public function numtothaistring($num)
    {
        $return_str = "";
        $txtnum1 = array('', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
        $txtnum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
        $num_arr = str_split($num);
        $count = count($num_arr);
        foreach ($num_arr as $key => $val) {
            // echo $count." ".$val." ".$key."</br>";

            if ($count > 1 && $val == 1 && $key == ($count - 1)) {
                $return_str .= "เอ็ด";
            } else if ($count > 1 && $val == 1 && $key == 2) {
                $return_str .= $txtnum2[$val];
            } else if ($count > 1 && $val == 2 && $key == ($count - 2)) {
                $return_str .= "ยี่" . $txtnum2[$count - $key - 1];
            } else if ($count > 1 && $val == 0 && $key == 0) {
                $return_str.=$txtnum2[$val];
            } else {
                $return_str .= $txtnum1[$val] . $txtnum2[$count - $key - 1];
            }
        }
        return $return_str;

    }
}
