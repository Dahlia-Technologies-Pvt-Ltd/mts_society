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
        Otherwise, please click this link to change your password: [RESET_LINK].';

        EmailTemplate::create([
            'template_code' => 'FORGOT_PASSWORD',
            'title' => 'Forgot Password',
            'content' => $forgot_password_content,
            'subject' => 'Your password reset link',
            'template_variable' =>  json_encode(['USER_NAME','RESET_LINK','OTP']), 
        ]);
 //############################################LOGIN OTP############################################
        $send_otp_content = 'Hi [USER_NAME], <br>
        your otp is :[OTP]';

        EmailTemplate::create([
            'template_code' => 'SEND_OTP',
            'title' => 'Send Otp',
            'content' => $send_otp_content,
            'subject' => 'Your otp',
            'template_variable' =>  json_encode(['USER_NAME','OTP']), 
        ]);
         //############################################congrats email############################################
         $Congrats_content = 'Congrats [USER_NAME], <br>
         you have been successfully registered in your society';
 
         EmailTemplate::create([
             'template_code' => 'CONGRATS',
             'title' => 'Congrats',
             'content' => $Congrats_content,
             'subject' => 'Congratulation mail',
             'template_variable' =>  json_encode(['USER_NAME']), 
         ]);
    }
}
