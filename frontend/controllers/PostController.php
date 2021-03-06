<?php

namespace frontend\controllers;

use common\models\PostExtendsModel;
use Yii;
use common\models\CatsModel;
use frontend\models\PostForm;
use yii\helpers\Markdown;

/**
 * 基础控制器
 */
Class PostController extends BaseController
{

    public function beforeAction($action)
    {
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'common\widgets\file_upload\UploadAction',     //这里扩展地址别写错
                'config' => [
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}",
                ]
            ],
            'ueditor' => [
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config' => [
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }

    // 文章列表
    public function actionIndex()
    {
        return $this->render('index');
    }

    // 创建文章
    public function actionCreate()
    {
        $model = new PostForm();
        $cat = CatsModel::getAllCats();

        //定义场景
        $model->setScenario(PostForm::SCENARIOS_CREATE);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (!$model->create()) {
                Yii::$app->session->setFlash('warning', $model->_lastError);
            } else {
                return $this->redirect(['post/view', 'id' => $model->id]);
            }
        }

        return $this->render('create', ['model' => $model, 'cat' => $cat]);
    }

    // 文章详情
    public function actionView($id)
    {
        $model = new PostForm();
        $data = $model->getViewById($id);


        $data['content'] = Markdown::process($data['content'],'gfm');

        //文章统计
        $model = new PostExtendsModel();
        $model->upCounter(['post_id' => $id], 'browser', 1);

        return $this->render('view', ['data' => $data]);
    }
}
