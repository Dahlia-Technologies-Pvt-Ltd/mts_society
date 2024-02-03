<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\CommonMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Master\{EmailTemplate};
use App\Models\{EmailLog};

class MailHelper
{
    //public static function sendMail($to, $subject, $view, $data = [], $attachments = [])
    public static function sendMail($templateCode = 'x', $data = [], $attachments = [])
    {
        $data = self::getEmailData($templateCode, $data, $attachments);
        $MailTo = isset($data['EMAIL']) && !empty($data['EMAIL']) ? $data['EMAIL'] : '';
        if (!empty($MailTo)) {
            $mailobj = Mail::to($MailTo);
            if (isset($data['cc']) && !empty($data['cc'])) {
                $mailobj->cc($data['cc']);
            }
            $mailobj->send(new CommonMail($data));
        } else {
            return false;
        }
    }

    /**
     * Get Email Data
     *
     * @return void
     */
    public static function getEmailData($templateCode = 'x', $data = [], $attachments = [])
    {

        $template =EmailTemplate::where('template_code', $templateCode)->first();
        if (!isset($template->content)) {
            return false;
        }
        //write query to fetch template
        $subject = $template->subject;
        $TemplateData = $template->content;
        $data['mailContent'] = preg_replace_callback('/\[([^\]]+)]/', function (array $matches) use ($data): string {
            $key = $matches[1];
            if (array_key_exists($key, $data)) {
                return isset($data[$key]) && $data[$key] !== null ? $data[$key] : '';
            }
            return $matches[0];
        }, $TemplateData);

        $data['Subject'] =   preg_replace_callback('/\[([^\]]+)]/', function (array $matches) use ($data): string {
            $key = $matches[1];
            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
            return $matches[0];
        }, $subject);

        return  $data;
    }

    /**
     * Save Email Template
     *
     * @param string $templateCode
     * @param array $data
     * @param array $attachments
     * @return void
     */
    public static function saveEmailTemplate($templateCode = 'x', $data = [], $attachments = [])
    {
        $data = self::getEmailData($templateCode, $data, $attachments);
        if (isset($data['EMAIL']) && is_array($data['EMAIL'])) {
            $MailTo = json_encode($data['EMAIL']);
        } else {
            $MailTo = $data['EMAIL'];
        }
        $template = DB::table('email_templates')->where('template_code', $templateCode)->first();
        $user_id = Auth::user()->id;
        $emailLog = new EmailLog();
        $ins_arr = [
            'template_code'                        => $template->template_code,
            'title'                        => $template->title,
            'content'                     => $template->content,
            'subject'                          => $template->subject,
            'template_variable'                          => $template->template_variable,
            'to_email'                          => $MailTo,
            'cc_email'                          => isset($template->cc_email) ? $template->cc_email : null,
            'bcc_email'                          => isset($template->bcc_email) ? $template->bcc_email : null,
            'from_name'                          => isset($template->from_name) ? $template->from_name : null,
            'notes'                          => isset($template->notes) ? $template->notes : null,
            'table_name'                          => isset($data['table_name']) ? $data['table_name'] : null,
            'table_id'                          => isset($data['table_id']) ? $data['table_id'] : null,
            'updated_by'                           => auth()->id(),
            'created_by' => auth()->id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        EmailLog::insert($ins_arr);
    }
}
