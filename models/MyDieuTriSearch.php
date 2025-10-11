<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Tìm kiếm điều trị theo mã KH, họ tên, ngày điều trị.
 */
class MyDieuTriSearch extends Model
{
    public $ma_the;
    public $ho_ten;
    public $ngay_dieu_tri;   // YYYY-MM-DD (input date)
    public $fixed_khId;      // giữ lọc KH từ controller (nếu có)

    public function rules(): array
    {
        return [
            [['ma_the', 'ho_ten', 'ngay_dieu_tri'], 'safe'],
            [['fixed_khId'], 'integer'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = MyDieuTri::find()
            ->joinWith(['khachHang kh']) // để lọc theo kh.ma_the, kh.ho_ten
            ->orderBy(['ngay_dieu_tri' => SORT_DESC, 'id' => SORT_DESC]);

        // Nếu truyền sẵn khId (từ trang KH)
        if ($this->fixed_khId) {
            $query->andWhere(['dieu_tri.id_kh' => (int)$this->fixed_khId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        // Gán params
        if (!$this->load($params) || !$this->validate()) {
            return $dataProvider;
        }

        // Lọc Mã KH (like)
        if (trim((string)$this->ma_the) !== '') {
            $query->andFilterWhere(['like', 'kh.ma_the', $this->ma_the]);
        }

        // Lọc Họ tên (like)
        if (trim((string)$this->ho_ten) !== '') {
            $query->andFilterWhere(['like', 'kh.ho_ten', $this->ho_ten]);
        }

        // Lọc Ngày (bằng đúng ngày)
        if (trim((string)$this->ngay_dieu_tri) !== '') {
            // CSDL của bạn đang lưu kiểu DATE 'Y-m-d'
            $query->andFilterWhere(['dieu_tri.ngay_dieu_tri' => $this->ngay_dieu_tri]);
        }

        return $dataProvider;
    }
}
