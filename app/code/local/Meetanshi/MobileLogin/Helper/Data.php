<?php
require_once(Mage::getBaseDir('lib') . '/twilio-php-master/Twilio/autoload.php');

use Twilio\Rest\Client;

class Meetanshi_MobileLogin_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ENABLED = 'mobilelogin/general/enabled';
    const IS_SEND_FORGOT = 'mobilelogin/otpsend/forgotenabled';
    const IS_SEND_LOGIN = 'mobilelogin/otpsend/loginenabled';
    const IS_SEND_REGISTER = 'mobilelogin/otpsend/registerenabled';
    const IS_SEND_UPDATE = 'mobilelogin/otpsend/updateenabled';


    const REGISTER_OTP_MSG = 'mobilelogin/otpsend/registrationmessage';
    const FORGOT_OTP_MSG = 'mobilelogin/otpsend/forgototpmessage';
    const LOGIN_OTP_MSG = 'mobilelogin/otpsend/loginotppmessage';
    const UPDATE_OTP_MSG = 'mobilelogin/otpsend/updateotpmessage';
    const OTP_LENGTH = 'mobilelogin/general/otplength';
    const SYSTEM_OTP_TYPE = 'mobilelogin/general/otptype';

    const APITYPE = 'mobilelogin/apiconfig/apiprovider';
    const SMS_SENDERID = 'mobilelogin/apiconfig/sendername';
    const SMS_MSGTYPE = 'mobilelogin/apiconfig/msgtype';
    const SMS_APIKEY = 'mobilelogin/apiconfig/authkey';
    const SMS_APIURL = 'mobilelogin/apiconfig/apiurl';
    const SID = 'mobilelogin/apiconfig/sid';
    const TOKEN = 'mobilelogin/apiconfig/token';
    const FROMMOBILE = 'mobilelogin/apiconfig/frommobile';
    const COUNTRYCODE = 'mobilelogin/apiconfig/countrycode';


    public function smsEnable()
    {
        return Mage::getStoreConfig(self::ENABLED);
    }

    public function getMobileLength()
    {
        return Mage::getStoreConfig(self::MOBILE_LENGTH);
    }

    public function getApitype()
    {
        return Mage::getStoreConfig(self::APITYPE);
    }

    public function getOtpLength()
    {
        return Mage::getStoreConfig(self::OTP_LENGTH);
    }

    public function generateOtpCode()
    {
        $length = Mage::getStoreConfig(self::OTP_LENGTH);
        $otptype = Mage::getStoreConfig(self::SYSTEM_OTP_TYPE);
        if ($otptype == 0) {
            $randomString = substr(str_shuffle("0123456789"), 0, $length);
        } elseif ($otptype == 1) {
            $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        } else {
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        }

        return $randomString;
    }

    public function Verifyotp($data)
    {
        $mobilenumber = $data['mobilenumber'];
        $verifytype = $data['verifytype'];
        $otpcode = $data['otpcode'];
        if (isset($data['oldmobile'])) {
            $oldMobilenumber = $data['oldmobile'];
        } else {
            $oldMobilenumber = $mobilenumber;
        }
        $return['succeess'] = "";
        $return['errormsg'] = "";
        try {
            $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
            $mobileloginModel->load($mobilenumber, 'mobilenumber');
            if ($verifytype == "register") {
                if ($mobileloginModel['register_otp'] == $otpcode) {
                    $mobileloginModel->setRegisterVerify(1)
                        ->save();
                    $return['succeess'] = "Register Otp Verified Succeess";
                } else {
                    $return['errormsg'] = "Invalid Otp";
                }
            } elseif ($verifytype == "forgot") {
                if ($mobileloginModel['forgot_otp'] == $otpcode) {
                    $mobileloginModel->setForgotVerify(1)
                        ->save();
                    $return['succeess'] = "Forgot Otp Verified Succeess";
                } else {
                    $return['errormsg'] = "Invalid Otp";
                }
            } elseif ($verifytype == "login") {
                if ($mobileloginModel['login_otp'] == $otpcode) {
                    $mobileloginModel->setLoginVerify(1)
                        ->save();
                    $collection = Mage::getModel('customer/customer')->getCollection();
                    $collection->addAttributeToFilter('mobile_number', array('eq' => $mobilenumber));
                    $custData = $collection->getData();
                    $customer = Mage::getModel('customer/customer')->load($custData[0]['entity_id']);
                    Mage::getSingleton('customer/session')->loginById($customer->getId());
                    $return['succeess'] = "Login Otp Verified Succeess";
                } else {
                    $return['errormsg'] = "Invalid Otp";
                }
            } elseif ($verifytype == "update") {
                $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
                $mobileloginModel->load($oldMobilenumber, 'mobilenumber');
                if ($mobileloginModel['update_otp'] == $otpcode) {
                    if ($oldMobilenumber != $mobilenumber) {
                        $collection = Mage::getModel('customer/customer')->getCollection();
                        $collection->addAttributeToFilter('mobile_number', array('eq' => $oldMobilenumber));
                        $custData = $collection->getData();
                        $customer = Mage::getModel('customer/customer');
                        $customer->load($custData[0]['entity_id']);
                        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                        $customer->setMobileNumber($mobilenumber)->save();
                        $mobileloginModel->setUpdateVerify(1)
                            ->setMobilenumber($mobilenumber)
                            ->save();
                        Mage::getSingleton("core/session")->addSuccess("Mobile Updated from " . $oldMobilenumber . " to " . $mobilenumber . " Succeessfully");
                    } else {
                        $customer = Mage::getSingleton('customer/session')->getCustomer();
                        $customer->setMobileNumber($mobilenumber)->save();
                        $mobileloginModel->setUpdateVerify(1)
                            ->setMobilenumber($mobilenumber)
                            ->save();
                        Mage::getSingleton("core/session")->addSuccess("Mobile Number Updated Succeessfully");
                    }
                    $return['succeess'] = "true";
                } else {
                    $return['errormsg'] = "Invalid Otp";
                }
            }
            print_r(json_encode($return));
        } catch (Exception $e) {
            Mage::log("Verify Otp :" . $e->getMessage(), null, 'Mobilelogin.log');
        }
    }

    public function getCurrentMobile()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()):
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            return $customer->getMobileNumber();
        endif;
    }

    public function Otpsave($data)
    {
        $otptype = $data['logintype'];
        $mobilenumber = $data['mobilenumber'];
        $resendotp = $data['resendotp'];
        $oldMobilenumber = "0000000000";
        if (isset($data['oldmobile'])) {
            $oldMobilenumber = $data['oldmobile'];
        } else {
            $oldMobilenumber = $mobilenumber;
        }
        $otpcode = $this->generateOtpCode();
        $return['succeess'] = "";
        $return['errormsg'] = "";
        $sendMsg = false;
        try {
            if ($otptype == "register") {
                $collection = Mage::getModel('customer/customer')->getCollection();
                $collection->addAttributeToFilter('mobile_number', array('eq' => $mobilenumber));

                if ($collection->getSize()) {
                    $return['errormsg'] = "Mobile Number is already Registered";
                    $return['succeess'] = "false";
                } else {
                    $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
                    $mobileloginModel->load($mobilenumber, 'mobilenumber');
                    if (!count($mobileloginModel->getData())) {
                        $mobileloginModel->setMobilenumber($mobilenumber);
                    }
                    $mobileloginModel->setRegisterOtp($otpcode);
                    $mobileloginModel->setRegisterVerify(0);
                    $mobileloginModel->save();
                    $sendMsg = true;
                }
            } elseif ($otptype == "forgot") {
                $collection = Mage::getModel('customer/customer')->getCollection();
                $collection->addAttributeToFilter('mobile_number', array('eq' => $mobilenumber));
                if (!$collection->getSize()) {
                    $return['errormsg'] = "Mobile Number is Not Registered";
                    $return['succeess'] = "false";
                } else {
                    $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
                    $mobileloginModel->load($mobilenumber, 'mobilenumber');
                    if ($mobilenumber == $mobileloginModel['mobilenumber']) {
                        $mobileloginModel->setForgotOtp($otpcode)
                            ->setForgotVerify(0)
                            ->save();
                        $sendMsg = true;
                    }
                }
            } elseif ($otptype == "login") {
                $collection = Mage::getModel('customer/customer')->getCollection();
                $collection->addAttributeToFilter('mobile_number', array('eq' => $mobilenumber));
                if (!$collection->getSize()) {
                    $return['errormsg'] = "Mobile Number is Not Registered Please Register";
                    $return['succeess'] = "false";
                } else {
                    $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
                    $mobileloginModel->load($mobilenumber, 'mobilenumber');
                    if ($mobilenumber == $mobileloginModel['mobilenumber']) {
                        $mobileloginModel->setLoginOtp($otpcode)
                            ->setLoginVerify(0)
                            ->save();
                        $sendMsg = true;
                    }
                }
            } elseif ($otptype == "update") {
                $collection = Mage::getModel('customer/customer')->getCollection();
                $collection->addAttributeToFilter('mobile_number', array('eq' => $mobilenumber));

                if ($collection->getSize()) {
                    $return['errormsg'] = "Mobile Number is already in use";
                    $return['succeess'] = "false";
                } else {
                    $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
                    $mobileloginModel->load($oldMobilenumber, 'mobilenumber');
                    if ($oldMobilenumber == $mobileloginModel['mobilenumber']) {
                        $mobileloginModel->setUpdateOtp($otpcode)
                            ->setUpdateVerify(0)
                            ->save();
                        $sendMsg = true;
                    } else {
                        $mobileloginModelNew = Mage::getModel('mobilelogin/mobilelogin');
                        $mobileloginModelNew->setMobilenumber($mobilenumber)
                            ->setUpdateOtp($otpcode)
                            ->setUpdateVerify(0)
                            ->save();
                        $sendMsg = true;
                    }
                }
            } else {
                $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
                $mobileloginModel->load($mobilenumber, 'mobilenumber');
                if ($resendotp or ($mobilenumber == $mobileloginModel['mobilenumber'])) {
                    $mobileloginModel->load($mobilenumber, 'mobilenumber');

                    if ($otptype == "register") {
                        $mobileloginModel->setRegisterOtp($otpcode)
                            ->setRegisterVerify(0)
                            ->save();
                    } elseif ($otptype == "forgot") {
                        $mobileloginModel->setForgotOtp($otpcode)
                            ->setForgotVerify(0)
                            ->save();
                    } elseif ($otptype == "login") {
                        $mobileloginModel->setLoginOtp($otpcode)
                            ->setLoginVerify(0)
                            ->save();
                    } elseif ($otptype == "update") {
                        $mobileloginModel->setUpdateOtp($otpcode)
                            ->setUpdateVerify(0)
                            ->save();
                    }
                }
            }

            if ($sendMsg) {
                $message = $this->getMessageText($otpcode, $otptype);
                if ($this->curlApi($mobilenumber, $message)) {
                    $return['succeess'] = "true";
                } else {
                    $return['errormsg'] = "Something Went wrong";
                }
            }
            print_r(json_encode($return));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'Mobilelogin.log');
        }
    }

    public function getMessageText($otpcode, $otptype)
    {
        $storename = Mage::app()->getStore()->getName();
        $storeUrl = Mage::getBaseUrl();
        $message = "";
        if ($otptype == "register") {
            $message = $regMsg = Mage::getStoreConfig(self::REGISTER_OTP_MSG);
        } elseif ($otptype == "forgot") {
            $message = $regMsg = Mage::getStoreConfig(self::FORGOT_OTP_MSG);
        } elseif ($otptype == "login") {
            $message = $regMsg = Mage::getStoreConfig(self::LOGIN_OTP_MSG);
        } elseif ($otptype == "update") {
            $message = $regMsg = Mage::getStoreConfig(self::UPDATE_OTP_MSG);
        }

        $replaceArray = array($otpcode, $storename, $storeUrl);
        $originalArray = array('{{otp_code}}', '{{shop_name}}', '{{shop_url}}');
        $newMessage = str_replace($originalArray, $replaceArray, $message);
        return $newMessage;
    }

    public function enabledLogin()
    {
        return (bool)Mage::getStoreConfigFlag(self::IS_SEND_LOGIN);
    }

    public function enabledForgot()
    {
        return (bool)Mage::getStoreConfigFlag(self::IS_SEND_FORGOT);
    }

    public function enabledRegister()
    {
        return (bool)Mage::getStoreConfigFlag(self::IS_SEND_REGISTER);
    }

    public function enabledUpdate()
    {
        return (bool)Mage::getStoreConfigFlag(self::IS_SEND_UPDATE);
    }

    public function curlApi($mobilenumber, $message)
    {
        try {
            if ($this->smsEnable()) {
                if ($this->getApitype() == "msg91") {
                    $msg = urlencode($message);
                    $apikey = Mage::getStoreConfig(self::SMS_APIKEY);
                    $senderid = Mage::getStoreConfig(self::SMS_SENDERID);
                    $url = Mage::getStoreConfig(self::SMS_APIURL);
                    $msgtype = Mage::getStoreConfig(self::SMS_MSGTYPE);

                    $postUrl = $url . "?sender=" . $senderid . "&route=" . $msgtype . "&mobiles=" . $mobilenumber . "&authkey=" . $apikey . "&message=" . $msg . "";
                    $curl = curl_init();
                    curl_setopt_array(
                        $curl, array(
                            CURLOPT_URL => $postUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_SSL_VERIFYPEER => 0,
                        )
                    );

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                        return "cURL Error #:" . $err;
                    } else {
                        return true;
                    }
                } elseif ($this->getApitype() == "textlocal") {

                    $url = Mage::getStoreConfig(self::SMS_APIURL);
                    $apiKey = urlencode(Mage::getStoreConfig(self::SMS_APIKEY));
                    $numbers = array($mobilenumber);
                    $sender = urlencode(Mage::getStoreConfig(self::SMS_SENDERID));
                    $message = rawurlencode($message);
                    $numbers = implode(',', $numbers);

                    $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $responseArray = json_decode($response);
                    if ($responseArray['status'] == "success") {
                        return true;
                    } else {
                        return false;
                    }
                } elseif ($this->getApitype() == "twilio") {
                    $sid = Mage::getStoreConfig(self::SID);
                    $token = Mage::getStoreConfig(self::TOKEN);
                    $fromMobile = Mage::getStoreConfig(self::FROMMOBILE);
                    $twilio = new Client($sid, $token);
                    $countryCode=Mage::getStoreConfig(self::COUNTRYCODE);
                    $message = $twilio->messages
                        ->create($countryCode.$mobilenumber, // to
                            array(
                                "body" => $message,
                                "from" => $fromMobile
                            )
                        );
                    if ($message->sid) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } catch (Exception $e) {
            return false;
            Mage::log($e->getMessage(), null, 'Mobilelogin.log');
        }
    }
}
