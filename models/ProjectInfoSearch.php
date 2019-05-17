<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProjectInfo;

/**
 * ProjectInfoSearch represents the model behind the search form of `app\models\ProjectInfo`.
 */
class ProjectInfoSearch extends ProjectInfo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'redis_port', 'redis_database_id'], 'integer'],
            [['project_name', 'project_key', 'redis_host', 'redis_password', 'create_time', 'update_time'], 'safe'],
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
        $query = ProjectInfo::find();

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
            'id' => $this->id,
            'redis_port' => $this->redis_port,
            'redis_database_id' => $this->redis_database_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'project_name', $this->project_name])
            ->andFilterWhere(['like', 'project_key', $this->project_key])
            ->andFilterWhere(['like', 'redis_host', $this->redis_host])
            ->andFilterWhere(['like', 'redis_password', $this->redis_password]);

        return $dataProvider;
    }
}
