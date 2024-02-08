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
             'template_code' => 'RESIDENT_USER_APPROVED',
             'title' => 'Congrats',
             'content' => $Congrats_content,
             'subject' => 'Congratulation mail',
             'template_variable' =>  json_encode(['USER_NAME']), 
         ]);
          //############################################ Invite Facility manager ############################################
         $invite_fmanager_content = "Dear [USER_NAME] <br>
         We are thrilled to invite you to join our exclusive community! Your participation means a lot to us, and we can't wait to have
         you on board. <br><br>
         
         To accept your invitation and become a part of our community, please follow these simple steps:<br>
         
         1. Click the Accept Invitation Button Below:<br>
         
         2. Create Your Account:<br>
         You will be directed to a registration page where you can set up your account.<br>
         Here, you'll need to choose a password for your account's security. Remember to keep your password safe and confidential.<br>
         
         3. Log In and Explore: <br>
         Use your registered email address and the newly created password to log in to your account. You can now access all the
         exciting features and resources our community has to offer.<br>
         Thank you for choosing to be a part of our community. We look forward to connecting with you and sharing valuable
         experiences together!<br>
                 Click this link  [INVITE_LINK] and get started!";
         
                
                 EmailTemplate::create([
                     'template_code' => 'FACILITY_MANAGER_SEND_INVITE',
                     'title' => 'Facility manager Send Invite Mail',
                     'content' => $invite_fmanager_content,
                     'subject' => 'Invitation to Create Your Account',
                     'template_variable' =>  json_encode(['USER_NAME','INVITE_LINK']),
                 ]);

                  //############################################ Thank you Invite To facility manager ############################################
        $thankyou_invite_fmanager = 'Welcome! [USER_NAME]
        your account is ready for use.';

        EmailTemplate::create([
            'template_code' => 'THANKYOU_FACILITY_MANAGER_SEND_INVITE',
            'title' => 'Thankyou Facility manager Send Invite Mail',
            'content' =>  $thankyou_invite_fmanager,
            'subject' => 'Your account has been activated',
            'template_variable' =>  json_encode(['USER_NAME']),
        ]);
    }
}
