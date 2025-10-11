<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

class MyKhachHangSearch extends MyKhachHang
{
    public $q; // tìm nhanh
    public $tinh_trang;
    public $last_ngay_dieu_tri;
    public $last_phi;
    public $last_tam_thu;

    /** Lọc theo nhiều nhóm (checkbox) — AND logic */
    public $nhom_ids = [];

    public function rules(): array
    {
        return [
            [['id','tong_phi','tam_thu','con_lai','nhom_kh'], 'integer'],
            [['ho_ten','sdt','ma_the','dia_chi','gioi_thieu','q'], 'safe'],
            [['ngay_sinh','ngay_hen','tinh_trang','last_ngay_dieu_tri'], 'safe'],
            ['nhom_ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search($params): ActiveDataProvider
    {
        $query = MyKhachHang::find()->alias('kh');

        // 1) Lấy ngày điều trị gần nhất mỗi KH
        $subDate = (new Query())
            ->select([
                'id_kh',
                'last_ngay_dieu_tri' => new Expression('MAX(ngay_dieu_tri)'),
            ])
            ->from('{{%dieu_tri}}')
            ->groupBy('id_kh');

        // 2) Trong ngày gần nhất đó, chọn bản ghi có id lớn nhất
        $subPick = (new Query())
            ->from(['dt2' => '{{%dieu_tri}}'])
            ->innerJoin(['md' => $subDate],
                'md.id_kh = dt2.id_kh AND md.last_ngay_dieu_tri = dt2.ngay_dieu_tri')
            ->select([
                'id_kh'   => 'dt2.id_kh',
                'last_id' => new Expression('MAX(dt2.id)'),
            ])
            ->groupBy('dt2.id_kh');

        // 3) Join vào KH
        $query->leftJoin(['md' => $subDate], 'md.id_kh = kh.id');                     // last_ngay_dieu_tri
        $query->leftJoin(['pick' => $subPick], 'pick.id_kh = kh.id');                 // last_id
        $query->leftJoin(['dt_item' => '{{%dieu_tri}}'], 'dt_item.id = pick.last_id'); // phi, tam_thu

        // 4) Select
        $query->select([
            'kh.*',
            'tinh_trang_sort'    => new Expression('CASE WHEN kh.con_lai > 0 THEN 1 ELSE 0 END'),
            'last_ngay_dieu_tri' => 'md.last_ngay_dieu_tri',
            'last_phi'           => 'dt_item.phi',
            'last_tam_thu'       => 'dt_item.tam_thu',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        // ===== Sort mapping =====
        $dataProvider->sort->attributes = array_merge(
            $dataProvider->sort->attributes,
            [
                'id'        => ['asc' => ['kh.id' => SORT_ASC],  'desc' => ['kh.id' => SORT_DESC]],
                'ho_ten'    => ['asc' => ['kh.ho_ten' => SORT_ASC], 'desc' => ['kh.ho_ten' => SORT_DESC]],
                'sdt'       => ['asc' => ['kh.sdt' => SORT_ASC],    'desc' => ['kh.sdt' => SORT_DESC]],
                'ma_the'    => ['asc' => ['kh.ma_the' => SORT_ASC], 'desc' => ['kh.ma_the' => SORT_DESC]],
                'con_lai'   => ['asc' => ['kh.con_lai' => SORT_ASC],'desc' => ['kh.con_lai' => SORT_DESC]],
                'ngay_sinh' => ['asc' => ['kh.ngay_sinh' => SORT_ASC],'desc' => ['kh.ngay_sinh' => SORT_DESC]],
                'ngay_hen'  => ['asc' => ['kh.ngay_hen' => SORT_ASC], 'desc' => ['kh.ngay_hen' => SORT_DESC]],
            ]
        );

        $dataProvider->sort->attributes['tinh_trang'] = [
            'asc'  => ['tinh_trang_sort' => SORT_ASC],
            'desc' => ['tinh_trang_sort' => SORT_DESC],
            'default' => SORT_ASC,
            'label' => 'Tình trạng',
        ];
        $dataProvider->sort->attributes['last_ngay_dieu_tri'] = [
            'asc'  => ['md.last_ngay_dieu_tri' => SORT_ASC,  'kh.id' => SORT_ASC],
            'desc' => ['md.last_ngay_dieu_tri' => SORT_DESC, 'kh.id' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['last_phi'] = [
            'asc' => ['dt_item.phi' => SORT_ASC],
            'desc'=> ['dt_item.phi' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['last_tam_thu'] = [
            'asc' => ['dt_item.tam_thu' => SORT_ASC],
            'desc'=> ['dt_item.tam_thu' => SORT_DESC],
        ];

        // ===== Load & validate =====
        $this->load($params);

        // Đảm bảo nhom_ids luôn là mảng
        if (!is_array($this->nhom_ids)) {
            $this->nhom_ids = $this->nhom_ids ? [$this->nhom_ids] : [];
        }

        if (!$this->validate()) {
            $this->applyDefaultOrderIfNoUserSort($query, $params);
            return $dataProvider;
        }

        // ===== AND logic cho nhóm: KH phải thuộc TẤT CẢ nhóm được chọn =====
        if (!empty($this->nhom_ids)) {
            $ids = array_values(array_unique(array_map('intval', $this->nhom_ids)));
            if ($ids) {
                $sub = (new Query())
                    ->select(['kn.kh_id'])
                    ->from('{{%kh_nhom_map}} kn')
                    ->where(['kn.nhom_id' => $ids])              // lọc theo các nhóm đã chọn
                    ->groupBy('kn.kh_id')
                    ->having(new Expression('COUNT(DISTINCT kn.nhom_id) = :n', [
                        ':n' => count($ids)
                    ]));                                         // phải đủ tất cả nhóm
                $query->andWhere(['kh.id' => $sub]);             // kh.id IN (subquery)
            }
        }

        // ===== Filters khác =====
        if ($this->q) {
            $q = trim($this->q);
            $query->andFilterWhere([
                'or',
                ['like', 'kh.ho_ten', $q],
                ['like', 'kh.sdt', $q],
                ['like', 'kh.ma_the', $q],
                ['like', 'kh.dia_chi', $q],
            ]);
        }

        $query->andFilterWhere(['kh.id' => $this->id]);
        $query->andFilterWhere(['like', 'kh.ho_ten', $this->ho_ten]);
        $query->andFilterWhere(['like', 'kh.sdt', $this->sdt]);
        $query->andFilterWhere(['like', 'kh.ma_the', $this->ma_the]);
        $query->andFilterWhere(['like', 'kh.dia_chi', $this->dia_chi]);

        // (Nếu vẫn dùng trường integer nhóm cũ)
        $query->andFilterWhere(['kh.nhom_kh' => $this->nhom_kh]);

        if (!empty($this->ngay_sinh)) {
            $query->andWhere(['DATE(kh.ngay_sinh)' => $this->ngay_sinh]);
        }
        if (!empty($this->ngay_hen))  {
            $query->andWhere(['DATE(kh.ngay_hen)' => $this->ngay_hen]);
        }

        if ($this->tinh_trang !== null && $this->tinh_trang !== '') {
            if ($this->tinh_trang === 'no') {
                $query->andWhere(['>',  'kh.con_lai', 0]);
            } elseif ($this->tinh_trang === 'yes') {
                $query->andWhere(['<=', 'kh.con_lai', 0]);
            }
        }

        if (!empty($this->last_ngay_dieu_tri)) {
            $query->andWhere(['md.last_ngay_dieu_tri' => $this->last_ngay_dieu_tri]); // DATE
        }

        // ===== Order mặc định nếu user chưa sort =====
        $this->applyDefaultOrderIfNoUserSort($query, $params);

        return $dataProvider;
    }

    /**
     * Thứ tự mặc định khi người dùng chưa bấm sort:
     * 1) KH chưa có ngày điều trị (NULL) lên trước
     * 2) Trong nhóm NULL: kh.id DESC
     * 3) Còn lại: last_ngay_dieu_tri DESC, kh.id DESC
     */
    private function applyDefaultOrderIfNoUserSort($query, $params): void
    {
        $hasUserSort = false;
        if (isset($params['sort']) && (string)$params['sort'] !== '') {
            $hasUserSort = true;
        } elseif (isset($_GET['sort']) && (string)$_GET['sort'] !== '') {
            $hasUserSort = true;
        }

        if (!$hasUserSort) {
            $query->addOrderBy(new Expression("
                CASE WHEN md.last_ngay_dieu_tri IS NULL THEN 0 ELSE 1 END ASC,
                CASE WHEN md.last_ngay_dieu_tri IS NULL THEN kh.id END DESC,
                md.last_ngay_dieu_tri DESC,
                kh.id DESC
            "));
        }
    }
}
