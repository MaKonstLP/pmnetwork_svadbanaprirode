<?php

namespace frontend\modules\svadbanaprirode\models;

use Yii;

/**
 * This is the model class for table "rooms".
 *
 * @property int $id
 * @property int $unique_id
 * @property int $sort_type
 */
class RoomsLocal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pmn_svadbanaprirode_dev.rooms_unique_id';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'unique_id'], 'required'],
            [['id', 'unique_id', 'sort_type'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

        ];
    }
}