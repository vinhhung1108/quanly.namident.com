<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DieuTri;

/**
 * DieuTriSearch represents the model behind the search form of `app\models\DieuTri`.
 */
class DieuTriSearch extends DieuTri
{
	public $dia_chi;
    public $ho_ten_khach_hang;
    public $ma_khach_hang;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'lan_dieu_tri', 'phi', 'tam_thu', 'id_kh'], 'integer'],
            [['ngay_dieu_tri', 'noi_dung', 'bs', 'gio','dia_chi','ho_ten_khach_hang', 'ma_khach_hang'], 'safe'],
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
		$query->joinWith('khach_hang');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$dataProvider->setSort([
            'defaultOrder' => ['ngay_dieu_tri'=>SORT_DESC,'gio'=>SORT_DESC],
        ]);
        $dataProvider->sort->attributes['ma_khach_hang']=[
            'asc'=> ['khach_hang.ma_the' => SORT_ASC],
            'desc'=> ['khach_hang.ma_the'=> SORT_DESC],
        ];
        $dataProvider->sort->attributes['ho_ten_khach_hang']=[
            'asc'=> ['khach_hang.ho_ten' => SORT_ASC],
            'desc'=> ['khach_hang.ho_ten'=> SORT_DESC],
        ];
        $dataProvider->sort->attributes['dia_chi']=[
            'asc'=> ['khach_hang.dia_chi' => SORT_ASC],
            'desc'=> ['khach_hang.dia_chi'=> SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }      
        $query->andFilterWhere([
            'dieu_tri.ngay_dieu_tri' => $this->ngay_dieu_tri,
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'lan_dieu_tri' => $this->lan_dieu_tri,
            'phi' => $this->phi,
            'tam_thu' => $this->tam_thu,
        ]);
		$query->andFilterWhere(['like','khach_hang.dia_chi',$this->dia_chi]);
        $query->andFilterWhere(['like', 'noi_dung', $this->noi_dung])
            ->andFilterWhere(['like', 'bs', $this->bs])
            ->andFilterWhere(['like','khach_hang.ho_ten', $this->ho_ten_khach_hang])
            ->andFilterWhere(['like', 'khach_hang.ma_the', $this->ma_khach_hang])
            ->andFilterWhere(['like', 'gio', $this->gio]);

        return $dataProvider;
    }

    public function searchlimit($params)
    {
        $query = DieuTri::find();

        // add conditions that should always apply here
		$query->joinWith('khach_hang');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=> [
                'pageSize'=>1000,
            ],
        ]);
		$dataProvider->setSort([
            'defaultOrder' => ['ngay_dieu_tri'=>SORT_DESC,'gio'=>SORT_DESC],
        ]);
        $dataProvider->sort->attributes['ma_khach_hang']=[
            'asc'=> ['khach_hang.ma_the' => SORT_ASC],
            'desc'=> ['khach_hang.ma_the'=> SORT_DESC],
        ];
        $dataProvider->sort->attributes['ho_ten_khach_hang']=[
            'asc'=> ['khach_hang.ho_ten' => SORT_ASC],
            'desc'=> ['khach_hang.ho_ten'=> SORT_DESC],
        ];
        $dataProvider->sort->attributes['dia_chi']=[
            'asc'=> ['khach_hang.dia_chi' => SORT_ASC],
            'desc'=> ['khach_hang.dia_chi'=> SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }      
        $query->andFilterWhere([
            'dieu_tri.ngay_dieu_tri' => $this->ngay_dieu_tri,
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'lan_dieu_tri' => $this->lan_dieu_tri,
            'phi' => $this->phi,
            'tam_thu' => $this->tam_thu,
        ]);
		$query->andFilterWhere(['like','khach_hang.dia_chi',$this->dia_chi]);
        $query->andFilterWhere(['like', 'noi_dung', $this->noi_dung])
            ->andFilterWhere(['like', 'bs', $this->bs])
            ->andFilterWhere(['like','khach_hang.ho_ten', $this->ho_ten_khach_hang])
            ->andFilterWhere(['like', 'khach_hang.ma_the', $this->ma_khach_hang])
            ->andFilterWhere(['like', 'gio', $this->gio]);

        return $dataProvider;
    }
}
