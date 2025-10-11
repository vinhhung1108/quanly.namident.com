<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class DichVuSearch extends DichVu
{
    public $q;

    public function rules(): array
    {
        return [
            [['id','don_gia'], 'integer'],
            [['ten','q'], 'safe'],
            [['active'], 'boolean'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search($params): ActiveDataProvider
    {
        $query = DichVu::find()->alias('dv')->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id','ten','don_gia','active','created_at','updated_at'
                ],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) return $dataProvider;

        // Tìm nhanh theo tên
        if ($this->q) {
            $q = trim($this->q);
            $query->andFilterWhere(['like', 'dv.ten', $q]);
        }

        $query->andFilterWhere(['dv.id' => $this->id]);
        $query->andFilterWhere(['dv.active' => $this->active]);
        $query->andFilterWhere(['dv.don_gia' => $this->don_gia]);
        $query->andFilterWhere(['like','dv.ten', $this->ten]);

        return $dataProvider;
    }
}
