<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\Session;

date_default_timezone_set('Asia/Bangkok');

class Vendor extends \common\models\Vendor
{
    public function behaviors()
    {
        return [
            'timestampcdate' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => time(),
            ],
            'timestampudate' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                ],
                'value' => time(),
            ],
            'timestampcby' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                ],
                'value' => Yii::$app->user->id,
            ],
            'timestamuby' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
                'value' => Yii::$app->user->id,
            ],
//            'timestampcompany'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'company_id',
//                ],
//                'value'=> isset($_SESSION['user_company_id'])? $_SESSION['user_company_id']:1,
//            ],
//            'timestampbranch'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'branch_id',
//                ],
//                'value'=> isset($_SESSION['user_branch_id'])? $_SESSION['user_branch_id']:1,
//            ],
            'timestampupdate' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => time(),
            ],
        ];
    }

    public static function findCode($id)
    {
        $model = Vendor::find()->where(['id' => $id])->one();
        return $model != null ? $model->code : '';
    }

    public static function findName($id)
    {
        $model = Vendor::find()->where(['id' => $id])->one();
        return $model != null ? $model->name : '';
    }

    public static function findPayMethod($id)
    {
        $model = Vendor::find()->where(['id' => $id])->one();
        return $model != null ? $model->payment_method_id : 0;
    }

    public static function findPayMethodName($id)
    {
        $name = '';
        $model = Vendor::find()->where(['id' => $id])->one();
        if($model != null){
            $model_x = \backend\models\Paymentmethod::find()->where(['id' => $model->payment_method_id])->one();
            if($model_x != null){
                $name = $model_x->name;
            }
        }
        return $name;
    }

    public static function findPayTerm($id)
    {
        $model = Vendor::find()->where(['id' => $id])->one();
        return $model != null ? $model->payment_term_id : 0;
    }
    public static function findPayTermName($id)
    {
        $name = '';
        $model = Vendor::find()->where(['id' => $id])->one();
        if($model != null){
            $model_x = \backend\models\PaymentTerm::find()->where(['id' => $model->payment_term_id])->one();
            if($model_x != null){
                $name = $model_x->name;
            }
        }
        return $name;
    }
    public static function findVendorphone($id)
    {
        $model = Vendor::find()->where(['id' => $id])->one();
        return $model != null ? $model->phone : 0;
    }
    public static function findContactName($id){
        $model = Vendor::find()->where(['id' => $id])->one();
        return $model != null ? $model->contact_name : "";
    }

    public static function findVendorlocation($id)
    {
        $model = Vendor::find()->where(['id' => $id])->one();
        return $model != null ? $model->location : 0;
    }
//    public function findName($id){
//        $model = Unit::find()->where(['id'=>$id])->one();
//        return count($model)>0?$model->name:'';
//    }
//    public function findUnitid($code){
//        $model = Unit::find()->where(['name'=>$code])->one();
//        return count($model)>0?$model->id:0;
//    }
    public static function getLastNo()
    {
        //   $model = Orders::find()->MAX('order_no');
        $model = Vendor::find()->MAX('code');

        $pre = "CU";

        if ($model != null) {
//            $prefix = $pre.substr(date("Y"),2,2);
//            $cnum = substr((string)$model,4,strlen($model));
//            $len = strlen($cnum);
//            $clen = strlen($cnum + 1);
//            $loop = $len - $clen;
            $prefix = $pre . '-' . substr(date("Y"), 2, 2);
            $cnum = substr((string)$model, 5, strlen($model));
            $len = strlen($cnum);
            $clen = strlen($cnum + 1);
            $loop = $len - $clen;
            for ($i = 1; $i <= $loop; $i++) {
                $prefix .= "0";
            }
            $prefix .= $cnum + 1;
            return $prefix;
        } else {
            $prefix = $pre . '-' . substr(date("Y"), 2, 2);
            return $prefix . '00001';
        }
    }


}
