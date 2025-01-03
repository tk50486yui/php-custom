<?php
require_once '../app/Core/BaseController.php';

class MyController extends BaseController
{
    public function __construct($response)
    {
        parent::__construct($response);
    }

    // $request, 預設最小筆數,  最大筆數, 資料總數
    protected function getPagination(Request $request, $defaultLimit = 20, $maxLimit = 100, $total = 100)
    {
        $limit = !empty($request->query('limit', '')) ? (int) $request->query('limit') : $defaultLimit;
        $page = !empty($request->query('page', '')) ? (int) $request->query('page') : 1;

        if ($limit < $defaultLimit) {
            $limit = $defaultLimit;
        } elseif ($limit > $maxLimit) {
            $limit = $maxLimit;
        }

        $totalPages = ceil($total / $limit);

        if ($page < 1) {
            $page = 1;
        } else if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $limit;

        return compact('limit', 'page', 'offset', 'totalPages', 'total');
    }

    public function getGuard($guardType)
    {
        $guard = new Guard($guardType);
        return [
            'id' => $guard->id,
            'name' => $guard->name,
            'level' => $guard->level,
        ];
    }

    public function renderOnline($isonline)
    {
        if (empty($isonline) || $isonline == 0) {
            return '<font color="red"><i class="fa fa-remove fa-2x"></i></font>';
        } else {
            return '<font color="green"><i class="fa fa-check fa-2x"></i></font>';
        }
    }

    public function renderImage($pic, $width = 100)
    {
        if (!empty($pic)) {
            return '<img src="' . Config::get('baseUpload') . $pic . '" style="max-width:' . $width . 'px;" />';
        } else {
            return '';
        }

    }

    public function renderReviewLabel($status)
    {
        switch ($status) {
            case 0:
                return '<span class="label label-warning">審核中</span>';
            case 1:
                return '<span class="label label-success">已通過</span>';
            case 2:
                return '<span class="label label-danger">未通過</span>';
            case 3:
                return '<span class="label label-default">未送審</span>';
            default:
                return '';
        }
    }

    public function renderLabelStatus($status)
    {
        switch ($status) {
            case 0:
                return '<span class="text-primary">庫存</span>';
            case 1:
                return '<span style="color:#019858">已核發</span>';
            case 2:
                return '<span class="text-danger">已註銷</span>';
            default:
                return '';
        }
    }

    public function renderReportStatus($status)
    {
        switch ($status) {
            case 0:
                return '<span class="text-danger">待處理</span>';
            default:
                return '<span class="text-primary">已處理</span>';
        }
    }

    public function renderReportFake($status)
    {
        switch ($status) {
            case 0:
                return '';
            case 1:
                return '<span class="text-info">否</span>';
            case 2:
                return '<span class="text-danger">偽品</span>';
            case 3:
                return '<span class="text-info">無法辨識</span>';
            default:
                return '';
        }
    }

    public function getReportFake($status)
    {
        switch ($status) {
            case 0:
                return '';
            case 1:
                return '否';
            case 2:
                return '偽品';
            case 3:
                return '無法辨識';
            default:
                return '';
        }
    }

    public function convertDate($val)
    {
        if (isset($val) && !empty($val)) {
            return date('Y-m-d', strtotime($val));
        }
        return '';
    }

}
