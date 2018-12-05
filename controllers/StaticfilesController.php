<?php

namespace app\controllers;

use app\models\SaveFilesModel;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\base\Controller;
use yii\web\NotFoundHttpException;


class StaticfilesController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'create' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $saveFilesModel = new SaveFilesModel();
        $result = $saveFilesModel->getBaseInfo();

        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('File not found!');
    }

    public function actionCreate()
    {
        $saveFilesModel = new SaveFilesModel();

        if ($saveFilesModel->uploadFile() && $saveFilesModel->save(false)) {
            return $saveFilesModel->attributes;
        }
        throw new BadRequestHttpException('Invalid query parameters, data not saved');
    }
}
