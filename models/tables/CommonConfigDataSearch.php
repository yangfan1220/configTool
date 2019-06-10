<?php

namespace app\models\tables;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\tables\CommonConfigData;

/**
 * CommonConfigDataSearch represents the model behind the search form of `app\models\tables\CommonConfigData`.
 */
class CommonConfigDataSearch extends CommonConfigData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'config_level', 'value_type'], 'integer'],
            [['key_value_mictime_md5', 'key', 'value', 'comment', 'create_name', 'modify_name', 'create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CommonConfigData::find();


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id'           => $this->id,
            'app_id'       => \Yii::$app->session['app_id'],
            'config_level' => $this->config_level,
            'value_type'   => $this->value_type,
            'create_time'  => $this->create_time,
            'update_time'  => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'create_name', $this->create_name])
            ->andFilterWhere(['like', 'modify_name', $this->modify_name]);

        $query->orderBy(['id'=>SORT_DESC]);

        return $dataProvider;
    }
}
