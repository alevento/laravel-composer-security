<?php
/**
 * Created by PhpStorm.
 * User: Alessandro
 * Date: 03/12/2015
 * Time: 17:25
 */

namespace Padosoft\Composer;

use Config;
use Validator;

class MailHelper
{

    protected $command;

    /**
     * MailHelper constructor.
     * @param Command $objcommand
     */
    public function __construct(Command $objcommand)
    {
        $this->command = $objcommand;
    }

    /**
     * @param $tuttoOk
     * @param $mail
     */
    public function sendEmail($tuttoOk, $mail)
    {
        $soggetto=Config::get('composer-security-check.mailSubjectSuccess');

        if (!$tuttoOk) {
            $soggetto=Config::get('composer-security-check.mailSubjetcAlarm');
        }

        //$mail = $this->option('mail');

        $validator = Validator::make(['email' => $mail], [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            $this->command->error('No valid email passed: '.$mail.'. Mail will not be sent.');
            return;
        }
        $this->command->line('Send email to <info>'.$mail.'</info>');

        $vul=$this->tableVulnerabilities;


        Mail::send(
            Config::get('composer-security-check.mailViewName'),
            ['vul' => $vul],
            function ($message) use ($mail, $soggetto) {
                $message->from(
                    Config::get('composer-security-check.mailFrom'),
                    Config::get('composer-security-check.mailFromName')
                );
                $message->to($mail, $mail);
                $message->subject($soggetto);
            }
        );


        $this->command->line('email sent.');

    }
}