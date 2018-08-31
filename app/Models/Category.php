<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 14:57
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Models;

use App\Library\Jd\Jd;

class Category extends Model
{
    public $timestamps = false;
    protected $fillable = ['sort', 'name', 'grade', 'parent_id', 'is_recommend'];

    public function syncCategory($parent_id = 0, $grade = 0)
    {
        $jd = new Jd();
        try {
            $data = $jd->request('jingdong.union.search.goods.category.query', [
                'parent_id' => $parent_id,
                'grade' => $grade
            ], 'querygoodscategory_result');
            if (!empty($data['data'])) foreach ($data['data'] as $value) {
                self::query()->updateOrCreate(['id' => $value['id']], [
                   'name' => $value['name'],
                   'grade' => $value['grade'],
                   'parent_id' => $value['parentId']
                ]);
                if ($grade <= 2) {
                    $this->syncCategory($value['id'], $grade + 1);
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 栏目列表
     * @param int $parent_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function lists($params)
    {
        $parent_id = data_get($params, 'parent_id', 0);
        $query = self::query();
        $query->where('parent_id', $parent_id);
        return $query->get();
    }

    public function edit($id, $params)
    {
        $params = array_only($params, $this->fillable);
        $res = self::query()->where(['id' => $id])->update($params);
        return $res ? self::query()->where('id', $id)->first() : [];
    }
}