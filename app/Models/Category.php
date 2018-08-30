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
    protected $fillable = ['id', 'name', 'grade', 'parent_id'];

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
}