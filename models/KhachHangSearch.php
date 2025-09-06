<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\KhachHang;

/**
 * KhachHangSearch represents the model behind the search form of `app\models\KhachHang`.
 */
class KhachHangSearch extends KhachHang
{
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tong_phi', 'tam_thu', 'con_lai','tam_thu_last','nhom_kh'], 'integer'],
            [['ho_ten', 'sdt', 'ngay_sinh', 'dieu_tri', 'ngay_hen', 'bs_dieu_tri', 'gioi_thieu', 'hinh_anh','ngay_dieu_tri', 'ma_the'], 'safe'],
            [['gio_hen','noi_dung_hen'], 'safe'],		
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
        $query = KhachHang::find();
		
        // add conditions that should always apply here
        $query->joinWith('nhomKhachHang');

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
            'ngay_sinh' => $this->ngay_sinh,
            'ngay_hen' => $this->ngay_hen,
            'tong_phi' => $this->tong_phi,
            'tam_thu' => $this->tam_thu,
            'con_lai' => $this->con_lai,
            'tam_thu_last' => $this->tam_thu_last,
            'gio_hen'=>$this->gio_hen,
        ]);
        if($this->nhom_kh != 0) {
            $query->andFilterWhere(['nhom_khach_hang.id' => $this->nhom_kh]);
        }

        $query->andFilterWhere(['like', 'ho_ten', $this->ho_ten])
            ->andFilterWhere(['like', 'sdt', $this->sdt])
            ->andFilterWhere(['like', 'dieu_tri', $this->dieu_tri])
            ->andFilterWhere(['like', 'bs_dieu_tri', $this->bs_dieu_tri])
            ->andFilterWhere(['like', 'gioi_thieu', $this->gioi_thieu])
            ->andFilterWhere(['like', 'ngay_dieu_tri', $this->ngay_dieu_tri])
            ->andFilterWhere(['like','noi_dung_hen', $this->noi_dung_hen])
			->andFilterWhere(['like', 'ma_the', $this->ma_the]);

        return $dataProvider;
    }
}
