<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ThuChi;

/**
 * ThuChiSearch represents the model behind the search form of `app\models\ThuChi`.
 */
class ThuChiThongke extends ThuChi
{
	public $ngay_bat_dau;
	public $ngay_ket_thuc;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ngay_bat_dau','ngay_ket_thuc','thu_chi','loai','nguoi_thu_chi_id'], 'safe'],
        ];
    }
	public function attributeLabels(){
		return [
			'ngay_bat_dau'=>'Ngày bắt đầu',
			'ngay_ket_thuc'=>'Ngày kết thúc',
			'loai'=>'Loại thu/chi',
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
    public function thongke($params)
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
		if($this->ngay_bat_dau <> null && $this->ngay_ket_thuc <> null) { 
			$query->andFilterWhere(['between','ngay_thu',$this->ngay_bat_dau,$this->ngay_ket_thuc]); 
		}else{
			if ($this->ngay_bat_dau <> null) {
				$query->andFilterWhere(['>=','ngay_thu',$this->ngay_bat_dau]);
			}else{
				if ($this->ngay_ket_thuc <> null){					
					$query->andFilterWhere(['<=','ngay_thu',$this->ngay_ket_thuc]);
				}
			}
		}
        $query->andFilterWhere([
            'id' => $this->id,			
            'so_tien' => $this->so_tien,
        ]);

        $query->andFilterWhere(['nguoi_thu_chi_id'=>$this->nguoi_thu_chi_id]);

        $query->andFilterWhere(['like', 'thu_chi', $this->thu_chi])			
            ->andFilterWhere(['like', 'noi_dung', $this->noi_dung]);
		if($this->loai <> '0'){$query->andFilterWhere(['like','loai', $this->loai]);}
				
		
        return $dataProvider;
    }
	/**Tongchi by kem**/
	public function tongchi($params)
    {
        $query = ThuChi::find();

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
		if($this->ngay_bat_dau <> null && $this->ngay_ket_thuc <> null) { 
			$query->andFilterWhere(['between','ngay_thu',$this->ngay_bat_dau,$this->ngay_ket_thuc]); 
		}else{
			if ($this->ngay_bat_dau <> null) {
				$query->andFilterWhere(['>=','ngay_thu',$this->ngay_bat_dau]);
			}else{
				if ($this->ngay_ket_thuc <> null){					
					$query->andFilterWhere(['<=','ngay_thu',$this->ngay_ket_thuc]);
				}
			}
		}
        $query->andFilterWhere([
            'id' => $this->id,			
            'so_tien' => $this->so_tien,
        ]);

        $query->andFilterWhere(['nguoi_thu_chi_id'=>$this->nguoi_thu_chi_id]);

        $query->andFilterWhere(['like', 'thu_chi', "Chi"]);
		if($this->loai <> '0'){$query->andFilterWhere(['like','loai', $this->loai]);}
		
		$tempt = $query->sum('so_tien');
		
        return $tempt;
    }
}
