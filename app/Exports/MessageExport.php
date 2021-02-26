<?php
namespace App\Exports;

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

class MessageExport extends StringValueBinder implements FromCollection,WithHeadings,ShouldAutoSize,WithCustomValueBinder
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
        return ['ID', 'UserName', 'UserNickName', 'Video', 'SendTime','Comment'];
    }

    /**
     * @param array $params
     * @return Collection
     * 返回结果集
     */
    public function collection($params=[]): Collection
    {
        $now      = Carbon::now();
        $params   = !empty($params) ? $params : $this->params;
        $endDate  = $now->endOfDay()->toDateTimeString();
        $dateTime =  $params['dateTime'];
        $allDate  = explode(' - ' , $dateTime);
        $start    = $allDate[0];
        $end      = $allDate[1];
        $start    = $start > $end     ? $end     : $start;
        $end      = $end   > $endDate ? $endDate : $end;

        $month  = date('Ym', strtotime($start));

        $table  = 'ry_messages_'.$month;
        $cTable = 'ry_chats_'.$month;
        $cTable = Schema::connection('lovbee')->hasTable($cTable) ? $cTable : 'ry_chats';
        $table  = Schema::connection('lovbee')->hasTable($table)  ? $table  : 'ry_messages';

        $sql    = "select u.user_id,u.user_name,u.user_nick_name,m.id, m.message_content, m.created_at from t_users u 
                inner join t_{$cTable} chats on chats.chat_from_id=u.user_id
                inner join t_{$table} m      on m.message_id=chats.chat_msg_uid
                where m.message_type='Helloo:VideoMsg' and m.created_at between '{$start}' and '{$end}'
                group by m.message_content order by m.id desc ";

        $result = DB::connection('lovbee')->select($sql);

        $comments = DB::table('message_comments')->whereIn('message_id', collect($result)->pluck('id')->toArray())->get();
        foreach ($result as $key=>$item) {
            if ($item->user_id=='290') {
                unset($result[$key]);
                continue;
            }
            $item->comment = '';
            $item->message_content = 'https://media.helloo.cn.mantouhealth.com/' . $item->message_content;
            foreach ($comments as $comment) {
                if ($comment->message_id == $item->id) {
                    $item->comment = $comment->comment;
                }
            }
            unset($item->id);
        }

        // 查询评论
        return collect($result)->values();
    }

}