<?php

require_once('./includes/enums/Register.class.php');
require_once('./includes/enums/Login.class.php');

class LoginService
{
    private $referral_id = 0;
    private $validation_id = 0;
    private $validation_key = 0;
    public $verify_url = '';
    public $login_id = 0;

    private function isRegisterDisabled(): bool
    {
        return Config::get()->reg_closed === 1;
    }

    private function isGameDisabled(): bool
    {
        return Config::get()->game_disable === 0;
    }

    private function checkSecretQuestion($sec_quest_id, $sec_quest_ans): int
    {
        global $LNG;
        if (!array_key_exists($sec_quest_id, $LNG['registerSecretQuestionArray']))
        {
            return Register::wrong_secret_question;
        }

        if (empty($sec_quest_ans))
        {
            return Register::empty_secret_question_ans;
        }

        if (strlen($sec_quest_ans) > 64)
        {
            return Register::too_long_secret_quest_ans;
        }

        return Register::secret_question_success;
    }

    private function createSecretQuestionAndAnswer(&$id, &$answer): void
    {
        global $LNG;
        $id = mt_rand(0, count($LNG['registerSecretQuestionArray']) - 1);
        $numbers = '0123456789';
        $upper_case = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $result = '';

        for ($i = 0; $i < 4; $i++)
        {
            $result .= $numbers[random_int(0, strlen($numbers) - 1)];
        }

        for ($i = 0; $i < 4; $i++)
        {
            $result .= $upper_case[random_int(0, strlen($upper_case) - 1)];
        }

        $answer = $result;
    }

    private function checkFormInput($user_name, $password, $email, $rules_checked)
    {
        if (empty($user_name))
        {
            return Register::user_name_empty;
        }

        if (empty($email))
        {
            return Register::mail_empty;
        }

        if (!PlayerUtil::isNameValid($user_name))
        {
            return Register::user_name_not_valid;
        }

        if (!PlayerUtil::isMailValid($email))
        {
            return Register::mail_not_valid;
        }

        if (strlen($email) > 64)
        {
            return Register::mail_too_long;
        }

        if (strlen($password) < 6)
        {
            return Register::pass_too_short;
        }

        if (strlen($password) > 20)
        {
            return Register::pass_too_long;
        }

        if ($rules_checked === 0)
        {
            return Register::rules_not_checked;
        }

        return Register::form_is_valid;
    }

    private function createUserName(&$user_name): void
    {
        $num = random_int(1000, 9999);
        $upper_case = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $user_name = 'userext_';
        // 4 upper case letters.
        for ($i = 0; $i < 4; $i++)
        {
            $user_name .= $upper_case[random_int(0, strlen($upper_case) - 1)];
        }

        $user_name .= '_' . $num;
    }

    private function createPassword(&$password): void
    {
        $numbers = '0123456789';
        $upper_case = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower_case = 'abcdefghijklmnopqrstuvwxyz';
        $symbols = '!@#$%&*_+';

        $result = '';

        // 8 num
        for ($i = 0; $i < 8; $i++)
        {
            $result .= $numbers[random_int(0, strlen($numbers) - 1)];
        }

        // 1x upper
        $result .= $upper_case[random_int(0, strlen($upper_case) - 1)];

        // 1x lower
        $result .= $lower_case[random_int(0, strlen($lower_case) - 1)];

        // 1x special
        $result .= $symbols[random_int(0, strlen($symbols) - 1)];

        $password = $result;
    }

    private function setRulesAsChecked(&$rules_checked): void
    {
        $rules_checked = 1;
    }

    private function existsUserNameInDB($user_name): bool
    {

        $sql = "SELECT (
				SELECT COUNT(*)
				FROM %%USERS%%
				WHERE universe = :universe
				AND username = :userName
			) + (
				SELECT COUNT(*)
				FROM %%USERS_VALID%%
				WHERE universe = :universe
				AND username = :userName
			) as count;";

        $count = Database::get()->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':userName' => $user_name,
        ]);

        if ($count === false)
        {
            throw new Exception('db error');
        }

        return (((int) $count['count']) ?? 0) > 0;
    }

    private function existsEmailInDB($email): bool
    {
        $sql = "SELECT COUNT(*) as count
        FROM %%USERS%%
		WHERE universe = :universe
		AND (email = :email OR email_2 = :email);";

        $count = Database::get()->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':email'    => $email,
        ]);

        if ($count === false)
        {
            // db error
            return true;
        }

        return (((int) $count['count']) ?? 0) > 0;
    }

    private function existsEmailInVerificationTable($email): bool
    {
        $sql = "SELECT COUNT(*) as count 
			FROM %%USERS_VALID%%
			WHERE universe = :universe
			AND email = :email";

        $count = Database::get()->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':email'    => $email,
        ]);

        if ($count === false)
        {
            throw new Exception('db error');
        }

        if (isset($count['count'])
            && $count['count'] > 0)
        {
            $this->refreshVerification($email);
            return true;
        }

        return false;
    }

    private function isCorrectCaptcha($external_auth): bool
    {
        $config = Config::get();

        if ($external_auth === true
            || $config->capaktiv !== '1'
            || !$config->use_recaptcha_on_register)
        {
            return true;
        }

        require_once('./includes/libs/reCAPTCHA/src/autoload.php');

        $recaptcha = new \ReCaptcha\ReCaptcha($config->capprivate);
        $resp = $recaptcha->verify(HTTP::_GP('g-recaptcha-response', ''), Session::getClientIp());
        if (!$resp->isSuccess())
        {
            return false;
        }

        return true;
    }

    private function setReferralID($id): void
    {
        if (Config::get()->ref_active != 1
            || $id === 0)
        {
            $this->referral_id = 0;
            return;
        }

        $sql = "SELECT COUNT(*) as state FROM %%USERS%% 
        WHERE id = :referral_id AND universe = :universe;";

        $count = Database::get()->selectSingle($sql, [
            ':referral_id' => $id,
            ':universe'    => Universe::current(),
        ]);

        if ($count === false)
        {
            $this->referral_id = 0;
            return;
        }

        if ((((int) $count['state']) ?? 0) > 0)
        {
            $this->referral_id = $id;
        }
    }

    private function saveToValidationTable(
        $user_name,
        $password,
        $email,
        $language,
        $user_secret_question_id,
        $user_secret_question_answer
    ): void {
        $this->validation_key = md5(uniqid('2m'));

        $sql = "INSERT INTO %%USERS_VALID%% SET
				`userName` = :user_name,
				`validationKey` = :validation_key,
				`password` = :password,
				`email` = :email,
				`date` = :timestamp,
				`ip` = :ip,
				`language` = :language,
				`universe` = :universe,
				`referralID` = :referral_id,
				`externalAuthUID` = :external_auth_uid,
				`externalAuthMethod` = :external_auth_method,
				`user_secret_question_id` = :user_secret_question_id,
				`user_secret_question_answer` = :user_secret_question_answer;";

        Database::get()->insert($sql, [
            ':user_name'                   => $user_name,
            ':validation_key'              => $this->validation_key,
            ':password'                    => PlayerUtil::cryptPassword($password),
            ':email'                       => $email,
            ':timestamp'                   => TIMESTAMP,
            ':ip'                          => Session::getClientIp(),
            ':language'                    => $language,
            ':universe'                    => Universe::current(),
            ':referral_id'                 => $this->referral_id,
            ':external_auth_uid'           => null,
            ':external_auth_method'        => null,
            ':user_secret_question_id'     => $user_secret_question_id,
            ':user_secret_question_answer' => $user_secret_question_answer,
        ]);

        $id = Database::get()->lastInsertId();

        if ($id !== false)
        {
            $this->validation_id = $id;
        }

    }

    private function refreshVerification($email): void
    {
        $this->validation_key = md5(uniqid('2m'));

        $sql = "SELECT validationID, userName FROM %%USERS_VALID%% WHERE email = :email;";
        $data = Database::get()->selectSingle($sql, [
            ':email' => $email,
        ]);

        if (!empty($data)
            && isset($data['validationID'])
            && $data['userName'])
        {
            $this->validation_key = md5(uniqid('2m'));
            $this->validation_id = $data['validationID'];

            $sql = "UPDATE %%USERS_VALID%%
               SET validationKey = :validation_key,
               date = :date_now
               WHERE validationID = :validation_id";

            Database::get()->update($sql, [
                ':validation_key' => $this->validation_key,
                ':validation_id'  => $data['validationID'],
                ':date_now'       => TIMESTAMP,
            ]);

            if ($this->shouldVerifyWithMail())
            {
                $this->sendVerificationMail($email, $data['userName']);
            }
        }
    }

    private function shouldVerifyWithMail(): bool
    {
        $config = Config::get();
        return $config->user_valid === 1
                && $config->mail_active === 1
                && $config->mail_use === 1;
    }

    private function sendVerificationMail($email, $user_name): void
    {
        global $LNG;
        $config = Config::get();
        $password = 'password';
        require './includes/classes/Mail.class.php';
        $MailRAW = $LNG->getTemplate('email_vaild_reg');
        $MailContent = str_replace([
            '{USERNAME}',
            '{PASSWORD}',
            '{GAMENAME}',
            '{VERTIFYURL}',
            '{GAMEMAIL}',
        ], [
            $user_name,
            $password,
            $config->game_name.' - '.$config->uni_name,
            HTTP_PATH.$this->verify_url,
            $config->smtp_sendmail,
        ], $MailRAW);

        $subject = sprintf($LNG['registerMailVertifyTitle'], $config->game_name);
        Mail::send($email, $user_name, $subject, $MailContent);
    }

    public function register(
        $user_name = '',
        $password = '',
        $email = '',
        $language = 'en',
        $rules_checked = 0,
        $csrf_token = '',
        $user_secret_question_id = 0,
        $user_secret_question_answer = '',
        $referral_id = 0,
        $external_auth = false
    ): int {

        if ($this->isGameDisabled())
        {
            return Register::game_disabled;
        }

        if ($this->isRegisterDisabled())
        {
            return Register::register_disabled;
        }

        if ($external_auth === true)
        {
            $this->createSecretQuestionAndAnswer(
                $user_secret_question_id,
                $user_secret_question_answer
            );

            do
            {
                $this->createUserName($user_name);
            }
            while ($this->existsUserNameInDB($user_name));

            $this->createPassword($password);
            $this->setRulesAsChecked($rules_checked);
        }

        $result = $this->checkSecretQuestion(
            $user_secret_question_id,
            $user_secret_question_answer
        );

        if ($result != Register::secret_question_success)
        {
            return $result;
        }

        $result = $this->checkFormInput($user_name, $password, $email, $rules_checked);

        if ($result != Register::form_is_valid)
        {
            return $result;
        }

        if (!isset($_COOKIE['csrfToken'])
            || $_COOKIE['csrfToken'] != $csrf_token)
        {
            return Register::csrf_wrong;
        }

        if ($this->existsUserNameInDB($user_name))
        {
            return Register::username_exists_in_db;
        }

        // TODO: inform, someone tried to register with your mail..
        if ($this->existsEmailInDB($email))
        {
            return Register::email_exists_in_db;
        }

        if ($this->existsEmailInVerificationTable($email))
        {
            return Register::register_success_verify_with_mail_existing;
        }

        if (!$this->isCorrectCaptcha($external_auth))
        {
            return Register::recaptcha_error;
        }

        $this->setReferralID($referral_id);

        $this->saveToValidationTable(
            $user_name,
            $password,
            $email,
            $language,
            $user_secret_question_id,
            $user_secret_question_answer
        );

        $this->verify_url = 'index.php?page=vertify&i=' .
        $this->validation_id .
        '&k=' .
        $this->validation_key;

        if ($this->shouldVerifyWithMail())
        {
            $this->sendVerificationMail($email, $user_name);
            return Register::register_success_verify_with_mail;
        }

        return Register::register_success_no_verify;
    }

    public function getJsonDataByResult($result): array
    {
        global $LNG;
        $data = [];
        $data['status'] = "fail";
        $data['msg'] = '';
        switch ($result)
        {
            case Register::game_disabled:
                $data['msg'] = $LNG['reg_err_1'];
                break;
            case Register::register_disabled:
                $data['msg'] = $LNG['reg_err_2'];
                break;
            case Register::wrong_secret_question:
                $data['msg'] = $LNG['reg_err_3'];
                break;
            case Register::empty_secret_question_ans:
                $data['msg'] = $LNG['reg_err_4'];
                break;
            case Register::too_long_secret_quest_ans:
                $data['msg'] = $LNG['reg_err_5'];
                break;
            case Register::user_name_empty:
                $data['msg'] = $LNG['reg_err_6'];
                break;
            case Register::user_name_not_valid:
                $data['msg'] = $LNG['reg_err_7'];
                break;
            case Register::pass_too_short:
                $data['msg'] = sprintf($LNG['reg_err_8'], 6);
                break;
            case Register::pass_too_long:
                $data['msg'] = $LNG['reg_err_9'];
                break;
            case Register::mail_not_valid:
                $data['msg'] = $LNG['reg_err_10'];
                break;
            case Register::mail_empty:
                $data['msg'] = $LNG['reg_err_11'];
                break;
            case Register::mail_too_long:
                $data['msg'] = $LNG['reg_err_12'];
                break;
            case Register::rules_not_checked:
                $data['msg'] = $LNG['reg_err_13'];
                break;
            case Register::csrf_wrong:
                $data['msg'] = $LNG['reg_err_14'];
                break;
            case Register::username_exists_in_db:
                $data['msg'] = $LNG['reg_err_15'];
                break;
            case Register::email_exists_in_db:
                $data['msg'] = $LNG['reg_err_16'];
                break;
            case Register::recaptcha_error:
                $data['msg'] = $LNG['reg_err_17'];
                break;
            case Register::register_success_verify_with_mail:
                $data['status'] = "success";
                $data['msg'] = $LNG['reg_suc_1'];
                break;
            case Register::register_success_no_verify:
                $data['status'] = "redirect";
                $data['url'] = $this->verify_url;
                break;
            case Register::register_success_verify_with_mail_existing:
                $data['status'] = "success";
                $data['msg'] = $LNG['reg_err_18'];
                break;
            default:
                $data['status'] = "fail";
                break;
        }
        return $data;
    }

    // LOGIN
    private function checkLoginInput($email, $password)
    {
        if (empty($email))
        {
            return Login::mail_empty;
        }

        if (empty($password))
        {
            return Login::password_empty;
        }

        return Login::form_is_valid;
    }

    private function verifyLogin(
        $email,
        $universe,
        $password,
        $token_val,
        $token_sel,
        $mem_email,
        $external_auth
    ) {
        $sql = "SELECT id, password 
        FROM %%USERS%% 
        WHERE email = :email AND universe = :universe;";

        $login_data = Database::get()->selectSingle($sql, [
            ':email'    => $email,
            ':universe' => $universe,
        ]);

        if ($login_data === false)
        {
            return Login::login_data_not_found;
        }

        $result = Login::unset_id;
        if ($external_auth === true)
        {
            $result = $this->verifyLoginExternal($login_data);
        }
        else
        {
            if (!empty($token_val)
                && !empty($token_sel)
                && $mem_email == $email)
            {
                $result = $this->verifyLoginWithToken($token_sel, $token_val, $email);
            }
            else
            {
                $result = $this->verifyLoginStandart($login_data, $password);
            }
        }

        if ($result == Login::verify_success_token
            || $result == Login::verify_success_st
            || $result == Login::verify_success_external)
        {
            $this->login_id = $login_data['id'];
        }

        return $result;

    }

    private function verifyLoginStandart($login_data, $password)
    {
        if (!isset($login_data['password']))
        {
            return Login::verify_st_wrong_data;
        }

        // send same msg.
        if (!password_verify($password, $login_data['password']))
        {
            return Login::verify_st_wrong_data;
        }

        return Login::verify_success_st;
    }

    private function verifyLoginExternal()
    {
        return Login::verify_success_external;
    }

    private function verifyLoginWithToken($token_sel, $token_val, $email)
    {
        $sql = "SELECT * FROM %%REMEMBER_ME%% 
        WHERE selector = :selector AND expiration_date > :time_now;";

        $token_full = Database::get()->selectSingle($sql, [
            ':selector' => $token_sel,
            ':time_now' => TIMESTAMP,
        ]);

        if (!$token_full)
        {
            return Login::verify_token_wrong_data;
        }

        if (!isset($token_full['hashed_validator']))
        {
            return Login::verify_token_wrong_data;
        }

        if (!password_verify($token_val, $token_full['hashed_validator']))
        {
            return Login::verify_token_not_match;
        }

        $sql = "SELECT email FROM %%USERS%% WHERE id = :user_id;";

        $email_check = Database::get()->selectSingle($sql, [
            ':user_id' => $token_full['user_id'],
        ], 'email');

        if (empty($email_check))
        {
            return Login::verify_token_no_email;
        }

        if ($email_check != $email)
        {
            return Login::verify_token_email_not_match;
        }

        return Login::verify_success_token;
    }

    public function verifyLoginCaptcha(): bool
    {
        $config = Config::get();
        if ($config->capaktiv === '1'
            && $config->use_recaptcha_on_login)
        {
            require_once('./includes/libs/reCAPTCHA/src/autoload.php');

            $recaptcha = new \ReCaptcha\ReCaptcha($config->capprivate);
            $resp = $recaptcha->verify(HTTP::_GP('g_recaptcha_response', ''), Session::getClientIp());
            if (!$resp->isSuccess())
            {
                return false;
            }
        }
        return true;
    }

    public function Login(
        $email = null,
        $password = null,
        $csrf_token = null,
        $token_val = null,
        $token_sel = null,
        $mem_email = null,
        $universe = null,
        $external_auth = false,
    ) {
        if (!isset($_COOKIE['csrfToken'])
            || $_COOKIE['csrfToken'] != $csrf_token)
        {
            return Login::csrf_wrong;
        }

        $result = $this->checkLoginInput($email, $password);

        if ($result != Login::form_is_valid)
        {
            return $result;
        }

        if (!$this->verifyLoginCaptcha())
        {
            return Login::login_captcha_wrong;
        }

        $result = $this->verifyLogin(
            $email,
            $universe,
            $password,
            $token_val,
            $token_sel,
            $mem_email,
            $external_auth
        );

        if (!in_array($result, [Login::verify_success_st, Login::verify_success_token, Login::verify_success_external]))
        {
            return $result;
        }

        return Login::success;
    }

    public function getJsonDataByResultLogin($result): array
    {
        global $LNG;

        $data = [];
        $data['status'] = "fail";
        $data['msg'] = '';

        switch ($result)
        {
            case Login::csrf_wrong:
                $data['msg'] = $LNG['log_err_1'];
                break;
            case Login::mail_empty:
                $data['msg'] = $LNG['log_err_3'];
                break;
            case Login::password_empty:
                $data['msg'] = $LNG['log_err_2'];
                break;
            case Login::login_captcha_wrong:
                $data['msg'] = $LNG['log_err_6'];
                break;
            case Login::login_data_not_found:
                $data['msg'] = $LNG['log_err_4'];
                break;
            case Login::unset_id:
                $data['msg'] = $LNG['log_err_0'];
                break;
            case Login::verify_token_wrong_data:
                $data['msg'] = $LNG['log_err_5'];
                break;
            case Login::verify_token_not_match:
                $data['msg'] = $LNG['log_err_5'];
                break;
            case Login::verify_token_no_email:
                $data['msg'] = $LNG['log_err_5'];
                break;
            case Login::verify_token_email_not_match:
                $data['msg'] = $LNG['log_err_5'];
                break;
            case Login::verify_st_wrong_data:
                $data['msg'] = $LNG['log_err_5'];
                break;
            case Login::success:
                $data['status'] = "redirect";
                $data['msg'] = $LNG['log_suc_1'];
                break;
            default:
                $data['status'] = "fail";
                break;
        }

        return $data;
    }

}
