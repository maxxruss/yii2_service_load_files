<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.10.18
 * Time: 20:58
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use Yii;

class SaveFilesModel extends ActiveRecord
{
    const MAX_LENGTH = 255;

    public $image;

    public static function tableName()
    {
        return 'static_files';
    }

    public function rules()
    {
        return [
            [['collection_id', 'user_id', 'file_id', 'coords'], 'required'],
            [['url'], 'string'],
            [['create_date'], 'safe'],
            [['collection_id'], 'string', 'max' => self::MAX_LENGTH],
            [['user_id'], 'integer', 'max' => self::MAX_LENGTH],
            [['file_id'], 'string', 'max' => self::MAX_LENGTH],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif'],
        ];
    }

    public function getBaseInfo()
    {
        $request = \Yii::$app->request;
        $collection_id = $request->get('collection_id');
        $file_id = $request->get('file_id');

        if (!$collection_id && !$file_id) {
            throw new BadRequestHttpException('You missed required params!');
        }

        $query = SaveFilesModel::find();

        if ($collection_id && $file_id) {
            $query
                ->where(['collection_id' => $collection_id, 'file_id' => $file_id]);
        }

        if ($collection_id) {
            $query
                ->where(['collection_id' => $collection_id]);
        }

        if ($file_id) {
            $query
                ->where(['file_id' => $file_id]);
        }

        return $query->all();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function uploadFile()
    {
        $this->image = UploadedFile::getInstanceByName('image');

        $this->setAttributes([
            'collection_id' => $this->getCollectionId(),
            'file_id' => $this->getFileId(),
            'user_id' => $this->getUserId(),
            'coords' => $this->getCoords(),
            'url' => $this->getUrl(),
        ]);

        if (!$this->validate()) {
            throw new BadRequestHttpException('saveAs invalid query parameters, data not saved');
        }
            $getCollectionPath = $this->getCollectionPath();
            if (!is_dir($getCollectionPath)) {
                mkdir($getCollectionPath);
            }
            $this->image->saveAs($this->getUrl());
            return true;

    }

    /**
     * @todo переделать получение collection_id на id из базы из 24 задачи
     * @return string
     */
    private function getCollectionId(): string
    {
        return hash('md5', Yii::$app->request->post('routeUser'));
    }

    /**
     * @return array
     */
    private function getCoords(): array
    {
        return json_decode(Yii::$app->request->post('coords'), true);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getUrl(): string
    {
        return $this->getCollectionPath() . '/' . $this->getFileId() . '.' . $this->image->extension;
    }

    /**
     * @todo переделать получение user_id на \Yii::$app->user->id
     * @return int
     */
    private function getUserId(): int
    {
        return Yii::$app->request->post('user_id');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getCollectionPath(): string
    {
        return \Yii::getAlias('@app') . '/files/' . $this->getCollectionId();
    }

    /**
     * @return string
     */
    private function getFileId(): string
    {
        return hash('md5', $this->getUserId() . time() . $this->image->baseName);
    }
}
