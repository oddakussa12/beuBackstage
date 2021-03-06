<?php
namespace App\Exports;

use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class UsersExport extends StringValueBinder implements FromCollection,WithHeadings,ShouldAutoSize,WithCustomValueBinder
{
    use Exportable;

    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return string[]
     * 设置header头
     */
    public function headings(): array
    {
        return ['user_id', 'user_avatar', 'user_name', 'user_nick_name', 'user_phone_country', 'user_phone','user_gender', 'country','user_register_at','ip','last_login_time','friend_count'];
    }

    /**
     * @param array $params
     * @return Collection
     * 返回结果集
     */
    public function collection($params=[]): Collection
    {
       $result = app(UserRepository::class)->findByWhere($params, true);
        // 查询评论
        return collect($result)->values();
    }

}