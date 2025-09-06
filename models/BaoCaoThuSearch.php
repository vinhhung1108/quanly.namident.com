<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DieuTri;

/**
 * DieuTriSearch represents the model behind the search form of `app\models\DieuTri`.
 */
class BaoCaoThuSearch extends DieuTri
{
	// public $dia_chi;
    public $tu_ngay;
    public $den_ngay;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'lan_dieu_tri', 'phi', 'tam_thu', 'id_kh'], 'integer'],
            [['ngay_dieu_tri', 'noi_dung', 'bs', 'gio','hinh_thuc_thanh_toan'], 'safe'],
            [['tu_ngay','den_ngay'], 'safe'],
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
        $query = DieuTri::find();

        // add conditions that should always apply here
		// $query->joinWith('khach_hang');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$dataProvider->setSort([
            'defaultOrder' => ['ngay_dieu_tri'=>SORT_DESC,'gio'=>SORT_DESC],
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
        ]);
        if($this->hinh_thuc_thanh_toan != 'all') { 
            $query->andFilterWhere(['hinh_thuc_thanh_toan' => $this->hinh_thuc_thanh_toan]);
        }
		// $query->andFilterWhere(['like','khach_hang.dia_chi',$this->dia_chi]);
        // $query->andFilterWhere(['like', 'noi_dung', $this->noi_dung])
        $query->andFilterWhere(['between','ngay_dieu_tri',$this->tu_ngay, $this->den_ngay]);

        if($this->bs != 'all') {
            $query->andFilterWhere(['like', 'bs', $this->bs]);
        }
            // ->andFilterWhere(['like', 'gio', $this->gio]);
        return $dataProvider;
    }

    /**Get total */
    public static function getTotal($provider, $fieldName)
    {
        $total = 0;

        foreach ($provider as $key => $item) {
            $total += $item[$fieldName];
        }

        return $total;
    }
}
