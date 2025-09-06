<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ThuChi;

/**
 * ThuChiSearch represents the model behind the search form of `app\models\ThuChi`.
 */
class ThuChiSearch extends ThuChi
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'so_tien'], 'integer'],
            [['thu_chi', 'ngay_thu', 'noi_dung','loai'], 'safe'],
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
        $query = ThuChi::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
		$dataProvider->setSort([
            'defaultOrder' => ['ngay_thu'=>SORT_DESC],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ngay_thu' => $this->ngay_thu,
            'so_tien' => $this->so_tien,			
        ]);

        $query->andFilterWhere(['like', 'thu_chi', $this->thu_chi])                        
			->andFilterWhere(['like', 'loai', $this->loai]);

        return $dataProvider;
    }
}
