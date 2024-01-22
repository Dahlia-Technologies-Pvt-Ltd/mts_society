<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use App\Models\Master\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('email_templates')->truncate();

        //############################################ FORGOT PASSWORD ############################################
        $forgot_password_content = 'Hi [USER_NAME], <br>
        There was a request to change your password!      <br>   
        If you did not make this request then please ignore this email. <br>       
        Otherwise, please click this link to change your password: [RESET_LINK]';

        EmailTemplate::create([
            'template_code' => 'FORGOT_PASSWORD',
            'title' => 'Forgot Password',
            'content' => $forgot_password_content,
            'subject' => 'Your password reset link',
            'template_variable' =>  json_encode(['USER_NAME','RESET_LINK']),
        ]);
    }
}
