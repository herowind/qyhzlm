<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究 )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 用户提现模型
// +----------------------------------------------------------------------
namespace app\core_api\model;

use app\common\model\CommonMod;

class UserWithdraw extends CommonMod
{

    public function getApplystatustextAttr($value, $data)
    {
        $arr = [
            '1' => '待审核',
            '2' => '审核通过',
            '0' => '审核拒绝'
        ];
        return $arr[$data['applystatus']];
    }

    public function getPaystatustextAttr($value, $data)
    {
        $arr = [
            '0' => '未打款',
            '1' => '已打款'
        ];
        return $arr[$data['applystatus']];
    }

    public function getProcesstextAttr($value, $data)
    {
        switch ($data['applystatus']) {
            case '1':
                return '审核中';
            case '2':
                if ($data['paystatus'] == 1) {
                    return '已完成';
                } else {
                    return '打款中';
                }
            case '0':
                return '已拒绝';
            default:
                return '异常';
        }
    }

    public function getListPageByUid($uid, $total = false)
    {
        $where['uid'] = $uid;
        $listPage = $this->field(true)
            ->where($where)
            ->order('id desc')
            ->paginate(10, $total);
        $pageData['list'] = $listPage->all();
        $pageData['page']['hasMore'] = $listPage->currentPage() < $listPage->lastPage();
        $pageData['page']['nextPage'] = $listPage->currentPage() + 1;
        $pageData['page']['total'] = $listPage->total();
        
        foreach ($pageData['list'] as $key => $val){
            $pageData['list'][$key]->append(['processtext']);
        }
        return $pageData;
    }
}