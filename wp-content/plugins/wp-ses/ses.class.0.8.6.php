<?php

/**
 * Parts copyright (c) 2016, Sylvain Deaure.
 * Copyright (c) 2014, Daniel Zahariev.
 * Copyright (c) 2011, Dan Myers.
 * Parts copyright (c) 2008, Donovan Schonknecht.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * This is a modified BSD license (the third clause has been removed).
 * The BSD license may be found here:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * Amazon Simple Email Service is a trademark of Amazon.com, Inc. or its affiliates.
 *
 * SimpleEmailService is based on Donovan Schonknecht's Amazon S3 PHP class, found here:
 * http://undesigned.org.za/2007/10/22/amazon-s3-php-class
 * and  https://github.com/daniel-zahariev/php-aws-ses
 *
 * @copyright 2016 Sylvain Deaure.
 * @copyright 2014 Daniel Zahariev
 * @copyright 2011 Dan Myers
 * @copyright 2008 Donovan Schonknecht
 */

/**
 * Amazon SimpleEmailService PHP class
 *
 * @version 0.8.6
 * @package AmazonSimpleEmailService
 */
class SimpleEmailService {

    protected $__accessKey; // AWS Access key
    protected $__secretKey; // AWS Secret key
    protected $__host;

    public function getAccessKey() {
        return $this->__accessKey;
    }

    public function getSecretKey() {
        return $this->__secretKey;
    }

    public function getHost() {
        return $this->__host;
    }

    protected $__verifyHost = true;
    protected $__verifyPeer = true;

    // verifyHost and verifyPeer determine whether curl verifies ssl certificates.
    // It may be necessary to disable these checks on certain systems.
    // These only have an effect if SSL is enabled.
    public function verifyHost() {
        return $this->__verifyHost;
    }

    public function enableVerifyHost($enable = true) {
        $this->__verifyHost = $enable;
    }

    public function verifyPeer() {
        return $this->__verifyPeer;
    }

    public function enableVerifyPeer($enable = true) {
        $this->__verifyPeer = $enable;
    }

    /**
     * Constructor
     *
     * @param string $accessKey Access key
     * @param string $secretKey Secret key
     * @param string $host Amazon Host through which to send the emails
     * @return void
     */
    public function __construct($accessKey = null, $secretKey = null, $host = 'email.us-east-1.amazonaws.com') {
        if ($accessKey !== null && $secretKey !== null) {
            $this->setAuth($accessKey, $secretKey);
        }
        $this->__host = $host;
    }

    /**
     * Set AWS access key and secret key
     *
     * @param string $accessKey Access key
     * @param string $secretKey Secret key
     * @return SimpleEmailService $this
     */
    public function setAuth($accessKey, $secretKey) {
        $this->__accessKey = $accessKey;
        $this->__secretKey = $secretKey;

        return $this;
    }

    /**
     * Lists the email addresses that have been verified and can be used as the 'From' address
     * Deprecated call
     *
     * @return array An array containing two items: a list of verified email addresses, and the request id.
     */
    public function listVerifiedEmailAddresses() {
        $rest = new SimpleEmailServiceRequest($this, 'GET');
        $rest->setParameter('Action', 'ListVerifiedEmailAddresses');

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('listVerifiedEmailAddresses', $rest->error);
            return false;
        }

        $response = array();
        if (!isset($rest->body)) {
            return $response;
        }

        $addresses = array();
        foreach ($rest->body->ListVerifiedEmailAddressesResult->VerifiedEmailAddresses->member as $address) {
            $addresses[] = (string) $address;
        }

        $response['Addresses'] = $addresses;
        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;

        return $response;
    }
    
     /**
     * Lists the email addresses and domains that have been verified and can be used as the 'From' address
     * Deprecated call
     *
     * @return array An array containing two items: a list of verified email addresses, and the request id.
     */
    public function listIdentities() {
        $rest = new SimpleEmailServiceRequest($this, 'GET');
        $rest->setParameter('Action', 'ListIdentities');

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('ListIdentities', $rest->error);
            return false;
        }

        $response = array();
        if (!isset($rest->body)) {
            return $response;
        }

        $identities = array();
        foreach ($rest->body->ListIdentitiesResult->Identities->member as $identity) {
            $identities[] = (string) $identity;
        }

        $response['Identities'] = $identities;
        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;

        return $response;
    }

    /**
     * Requests verification of the provided email address, so it can be used
     * as the 'From' address when sending emails through SimpleEmailService.
     *
     * After submitting this request, you should receive a verification email
     * from Amazon at the specified address containing instructions to follow.
     *
     * @param string $email The email address to get verified
     * @return array The request id for this request.
     */
    public function verifyEmailAddress($email) {
        // deprecated, use verifyEmailIdentity instead
        return $this->verifyEmailIdentity($email);
        /*
        $rest = new SimpleEmailServiceRequest($this, 'POST');
        // VerifyEmailAddress is deprecated
        $rest->setParameter('Action', 'VerifyEmailAddress');
        $rest->setParameter('EmailAddress', $email);

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('verifyEmailAddress', $rest->error);
            return false;
        }

        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;
        return $response;*/
    }
    
    /**
     * Requests verification of the provided email address, so it can be used
     * as the 'From' address when sending emails through SimpleEmailService.
     *
     * After submitting this request, you should receive a verification email
     * from Amazon at the specified address containing instructions to follow.
     *
     * @param string $email The email address to get verified
     * @return array The request id for this request.
     */
    public function verifyEmailIdentity($email) {
        $rest = new SimpleEmailServiceRequest($this, 'POST');
        // VerifyEmailAddress is deprecated
        $rest->setParameter('Action', 'VerifyEmailIdentity');
        $rest->setParameter('EmailAddress', $email);

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('VerifyEmailIdentity', $rest->error);
            return false;
        }

        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;
        return $response;
    }

    /**
     * Removes the specified email address from the list of verified addresses.
     *
     * @param string $email The email address to remove
     * @return array The request id for this request.
     */
    public function deleteVerifiedEmailAddress($email) {
        // deprectated, use deleteIdentity
        return $this->deleteIdentity($email);              
        /*
        $rest = new SimpleEmailServiceRequest($this, 'DELETE');
        $rest->setParameter('Action', 'DeleteVerifiedEmailAddress');
        $rest->setParameter('EmailAddress', $email);

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('deleteVerifiedEmailAddress', $rest->error);
            return false;
        }

        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;
        return $response;*/
    }
    /**
     * Removes the specified email address from the list of verified addresses.
     *
     * @param string $email The email address to remove
     * @return array The request id for this request.
     */
    public function deleteIdentity($email) {
        $rest = new SimpleEmailServiceRequest($this, 'DELETE');
        $rest->setParameter('Action', 'DeleteIdentity');
        $rest->setParameter('Identity', $email);

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('deleteIdentity', $rest->error);
            return false;
        }

        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;
        return $response;
    }

    /**
     * Retrieves information on the current activity limits for this account.
     * See http://docs.amazonwebservices.com/ses/latest/APIReference/API_GetSendQuota.html
     *
     * @return array An array containing information on this account's activity limits.
     */
    public function getSendQuota() {
        $rest = new SimpleEmailServiceRequest($this, 'GET');
        $rest->setParameter('Action', 'GetSendQuota');

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('getSendQuota', $rest->error);
            return false;
        }

        $response = array();
        if (!isset($rest->body)) {
            return $response;
        }

        $response['Max24HourSend'] = (string) $rest->body->GetSendQuotaResult->Max24HourSend;
        $response['MaxSendRate'] = (string) $rest->body->GetSendQuotaResult->MaxSendRate;
        $response['SentLast24Hours'] = (string) $rest->body->GetSendQuotaResult->SentLast24Hours;
        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;

        return $response;
    }

    /**
     * Retrieves statistics for the last two weeks of activity on this account.
     * See http://docs.amazonwebservices.com/ses/latest/APIReference/API_GetSendStatistics.html
     *
     * @return array An array of activity statistics.  Each array item covers a 15-minute period.
     */
    public function getSendStatistics() {
        $rest = new SimpleEmailServiceRequest($this, 'GET');
        $rest->setParameter('Action', 'GetSendStatistics');

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->triggerError('getSendStatistics', $rest->error);
            return false;
        }

        $response = array();
        if (!isset($rest->body)) {
            return $response;
        }

        $datapoints = array();
        foreach ($rest->body->GetSendStatisticsResult->SendDataPoints->member as $datapoint) {
            $p = array();
            $p['Bounces'] = (string) $datapoint->Bounces;
            $p['Complaints'] = (string) $datapoint->Complaints;
            $p['DeliveryAttempts'] = (string) $datapoint->DeliveryAttempts;
            $p['Rejects'] = (string) $datapoint->Rejects;
            $p['Timestamp'] = (string) $datapoint->Timestamp;

            $datapoints[] = $p;
        }

        $response['SendDataPoints'] = $datapoints;
        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;

        return $response;
    }

    /**
     * Given a SimpleEmailServiceMessage object, submits the message to the service for sending.
     *
     * @param SimpleEmailServiceMessage $sesMessage An instance of the message class
     * @param boolean $use_raw_request If this is true or there are attachments to the email `SendRawEmail` call will be used
     * @return array An array containing the unique identifier for this message and a separate request id.
     *         Returns false if the provided message is missing any required fields.
     */
    public function sendEmail($sesMessage, $use_raw_request = false) {
        if (!$sesMessage->validate()) {
            return false;
        }

        $rest = new SimpleEmailServiceRequest($this, 'POST');
        $action = !empty($sesMessage->attachments) || $use_raw_request ? 'SendRawEmail' : 'SendEmail';
        $rest->setParameter('Action', $action);

        if ($action == 'SendRawEmail') {
            $rest->setParameter('RawMessage.Data', $sesMessage->getRawMessage());
        } else {
            $i = 1;
            foreach ($sesMessage->to as $to) {
                $rest->setParameter('Destination.ToAddresses.member.' . $i, $sesMessage->encodeRecipients($to));
                $i++;
            }

            if (is_array($sesMessage->cc)) {
                $i = 1;
                foreach ($sesMessage->cc as $cc) {
                    $rest->setParameter('Destination.CcAddresses.member.' . $i, $sesMessage->encodeRecipients($cc));
                    $i++;
                }
            }

            if (is_array($sesMessage->bcc)) {
                $i = 1;
                foreach ($sesMessage->bcc as $bcc) {
                    $rest->setParameter('Destination.BccAddresses.member.' . $i, $sesMessage->encodeRecipients($bcc));
                    $i++;
                }
            }

            if (is_array($sesMessage->replyto)) {
                $i = 1;
                foreach ($sesMessage->replyto as $replyto) {
                    $rest->setParameter('ReplyToAddresses.member.' . $i, $sesMessage->encodeRecipients($replyto));
                    $i++;
                }
            }

            $rest->setParameter('Source', $sesMessage->encodeRecipients($sesMessage->from));

            if ($sesMessage->returnpath != null) {
                $rest->setParameter('ReturnPath', $sesMessage->returnpath);
            }

            if ($sesMessage->subject != null && strlen($sesMessage->subject) > 0) {
                $rest->setParameter('Message.Subject.Data', $sesMessage->subject);
                if ($sesMessage->subjectCharset != null && strlen($sesMessage->subjectCharset) > 0) {
                    $rest->setParameter('Message.Subject.Charset', $sesMessage->subjectCharset);
                }
            }


            if ($sesMessage->messagetext != null && strlen($sesMessage->messagetext) > 0) {
                $rest->setParameter('Message.Body.Text.Data', $sesMessage->messagetext);
                if ($sesMessage->messageTextCharset != null && strlen($sesMessage->messageTextCharset) > 0) {
                    $rest->setParameter('Message.Body.Text.Charset', $sesMessage->messageTextCharset);
                }
            }

            if ($sesMessage->messagehtml != null && strlen($sesMessage->messagehtml) > 0) {
                $rest->setParameter('Message.Body.Html.Data', $sesMessage->messagehtml);
                if ($sesMessage->messageHtmlCharset != null && strlen($sesMessage->messageHtmlCharset) > 0) {
                    $rest->setParameter('Message.Body.Html.Charset', $sesMessage->messageHtmlCharset);
                }
            }
        }

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            echo("Error:\n");
            print_r($rest->error);
            $this->triggerError('sendEmail', $rest->error);
            return false;
        }

        if ($action == 'SendRawEmail') {
            $response['MessageId'] = (string) $rest->body->SendRawEmailResult->MessageId;
        } else {
            $response['MessageId'] = (string) $rest->body->SendEmailResult->MessageId;
        }
        $response['RequestId'] = (string) $rest->body->ResponseMetadata->RequestId;
        return $response;
    }

    /**
     * Trigger an error message
     *
     * {@internal Used by member functions to output errors}
     * @param  string $functionname The name of the function that failed
     * @param array $error Array containing error information
     * @return  void
     */
    public function triggerError($functionname, $error) {
        if ($error == false) {
            trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error, but no description given", $functionname), E_USER_WARNING);
        } else if (isset($error['curl']) && $error['curl']) {
            trigger_error(sprintf("SimpleEmailService::%s(): %s %s", $functionname, $error['code'], $error['message']), E_USER_WARNING);
        } else if (isset($error['Error'])) {
            $e = $error['Error'];
            $message = sprintf("SimpleEmailService::%s(): %s - %s: %s\nRequest Id: %s\n", $functionname, $e['Type'], $e['Code'], $e['Message'], $error['RequestId']);
            trigger_error($message, E_USER_WARNING);
        } else {
            trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error: %s", $functionname, $error), E_USER_WARNING);
        }
    }

}

/**
 * SimpleEmailServiceRequest PHP class
 *
 * @link https://github.com/daniel-zahariev/php-aws-ses
 * @version 0.8.3
 * @package AmazonSimpleEmailService
 */
final class SimpleEmailServiceRequest {

    private $ses, $verb, $parameters = array();
    public $response;
    public static $curlOptions = array();

    /**
     * Constructor
     *
     * @param string $ses The SimpleEmailService object making this request
     * @param string $verb HTTP verb
     * @return void
     */
    function __construct($ses, $verb) {
        $this->ses = $ses;
        $this->verb = $verb;
        $this->response = (object) array('body' => '', 'code' => 0, 'error' => false);
    }

    /**
     * Set request parameter
     *
     * @param string  $key Key
     * @param string  $value Value
     * @param boolean $replace Whether to replace the key if it already exists (default true)
     * @return SimpleEmailServiceRequest $this
     */
    public function setParameter($key, $value, $replace = true) {
        if (!$replace && isset($this->parameters[$key])) {
            $temp = (array) ($this->parameters[$key]);
            $temp[] = $value;
            $this->parameters[$key] = $temp;
        } else {
            $this->parameters[$key] = $value;
        }

        return $this;
    }

    /**
     * Get the response
     *
     * @return object | false
     */
    public function getResponse() {

        $params = array();
        foreach ($this->parameters as $var => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $params[] = $var . '=' . $this->customUrlEncode($v);
                }
            } else {
                $params[] = $var . '=' . $this->customUrlEncode($value);
            }
        }

        sort($params, SORT_STRING);

        // must be in format 'Sun, 06 Nov 1994 08:49:37 GMT'
        $date = gmdate('D, d M Y H:i:s e');

        $query = implode('&', $params);

        $headers = array();
        $headers[] = 'Date: ' . $date;
        $headers[] = 'Host: ' . $this->ses->getHost();

        $auth = 'AWS3-HTTPS AWSAccessKeyId=' . $this->ses->getAccessKey();
        $auth .= ',Algorithm=HmacSHA256,Signature=' . $this->getSignature($date);
        $headers[] = 'X-Amzn-Authorization: ' . $auth;

        $url = 'https://' . $this->ses->getHost() . '/';

        // Basic setup
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, 'SimpleEmailService/php');

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, ($this->ses->verifyHost() ? 2 : 0));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, ($this->ses->verifyPeer() ? 1 : 0));

        // Request types
        switch ($this->verb) {
            case 'GET':
                $url .= '?' . $query;
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->verb);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                break;
            case 'DELETE':
                $url .= '?' . $query;
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default: break;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_WRITEFUNCTION, array(&$this, 'responseWriteCallback'));
        @ curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        foreach (self::$curlOptions as $option => $value) {
            curl_setopt($curl, $option, $value);
        }
// SD (option ?)
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        // Execute, grab errors
        if (curl_exec($curl)) {
            $this->response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        } else {
            $this->response->error = array(
                'curl' => true,
                'code' => curl_errno($curl),
                'message' => curl_error($curl),
                'resource' => $this->resource
            );
        }

        @curl_close($curl);

        // Parse body into XML
        if ($this->response->error === false && !empty($this->response->body)) {
            $this->response->body = simplexml_load_string($this->response->body);

            // Grab SES errors
            if (!in_array($this->response->code, array(200, 201, 202, 204)) && isset($this->response->body->Error)) {
                $error = $this->response->body->Error;
                $output = array();
                $output['curl'] = false;
                $output['Error'] = array();
                $output['Error']['Type'] = (string) $error->Type;
                $output['Error']['Code'] = (string) $error->Code;
                $output['Error']['Message'] = (string) $error->Message;
                $output['RequestId'] = (string) $this->response->body->RequestId;

                $this->response->error = $output;
                unset($this->response->body);
            }
        }

        return $this->response;
    }

    /**
     * CURL write callback
     *
     * @param resource $curl CURL resource
     * @param string $data Data
     * @return integer
     */
    private function responseWriteCallback(&$curl, &$data) {
        $this->response->body .= $data;
        return strlen($data);
    }

    /**
     * Contributed by afx114
     * URL encode the parameters as per http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/index.html?Query_QueryAuth.html
     * PHP's rawurlencode() follows RFC 1738, not RFC 3986 as required by Amazon. The only difference is the tilde (~), so convert it back after rawurlencode
     * See: http://www.morganney.com/blog/API/AWS-Product-Advertising-API-Requires-a-Signed-Request.php
     *
     * @param string $var String to encode
     * @return string
     */
    private function customUrlEncode($var) {
        return str_replace('%7E', '~', rawurlencode($var));
    }

    /**
     * Generate the auth string using Hmac-SHA256
     *
     * @internal Used by SimpleDBRequest::getResponse()
     * @param string $string String to sign
     * @return string
     */
    private function getSignature($string) {
        return base64_encode(hash_hmac('sha256', $string, $this->ses->getSecretKey(), true));
    }

}

/**
 * SimpleEmailServiceMessage PHP class
 *
 * @link https://github.com/daniel-zahariev/php-aws-ses
 * @version 0.8.3
 * @package AmazonSimpleEmailService
 */
final class SimpleEmailServiceMessage {

    // these are public for convenience only
    // these are not to be used outside of the SimpleEmailService class!
    public $to, $cc, $bcc, $replyto, $recipientsCharset;
    public $from, $returnpath;
    public $subject, $messagetext, $messagehtml;
    public $subjectCharset, $messageTextCharset, $messageHtmlCharset;
    public $attachments = array(), $customHeaders = array();

    public function __construct() {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
        $this->replyto = array();
        $this->recipientsCharset = 'UTF-8';

        $this->from = null;
        $this->returnpath = null;

        $this->subject = null;
        $this->messagetext = null;
        $this->messagehtml = null;

        $this->subjectCharset = 'UTF-8';
        $this->messageTextCharset = 'UTF-8';
        $this->messageHtmlCharset = 'UTF-8';
    }

    /**
     * addTo, addCC, addBCC, and addReplyTo have the following behavior:
     * If a single address is passed, it is appended to the current list of addresses.
     * If an array of addresses is passed, that array is merged into the current list.
     */
    public function addTo($to) {
        if (!is_array($to)) {
            $this->to[] = $to;
        } else {
            $this->to = array_unique(array_merge($this->to, $to));
        }

        return $this;
    }

    public function addCC($cc) {
        if (!is_array($cc)) {
            $this->cc[] = $cc;
        } else {
            $this->cc = array_merge($this->cc, $cc);
        }

        return $this;
    }

    public function addBCC($bcc) {
        if (!is_array($bcc)) {
            $this->bcc[] = $bcc;
        } else {
            $this->bcc = array_merge($this->bcc, $bcc);
        }

        return $this;
    }

    public function addReplyTo($replyto) {
        if (!is_array($replyto)) {
            $this->replyto[] = $replyto;
        } else {
            $this->replyto = array_merge($this->replyto, $replyto);
        }

        return $this;
    }

    public function setFrom($from) {
        $this->from = $from;

        return $this;
    }

    public function setReturnPath($returnpath) {
        $this->returnpath = $returnpath;

        return $this;
    }

    public function setRecipientsCharset($charset) {
        $this->recipientsCharset = $charset;

        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    public function setSubjectCharset($charset) {
        $this->subjectCharset = $charset;

        return $this;
    }

    public function setMessageFromString($text, $html = null) {
        $this->messagetext = $text;
        $this->messagehtml = $html;

        return $this;
    }

    public function setMessageFromFile($textfile, $htmlfile = null) {
        if (file_exists($textfile) && is_file($textfile) && is_readable($textfile)) {
            $this->messagetext = file_get_contents($textfile);
        } else {
            $this->messagetext = null;
        }
        if (file_exists($htmlfile) && is_file($htmlfile) && is_readable($htmlfile)) {
            $this->messagehtml = file_get_contents($htmlfile);
        } else {
            $this->messagehtml = null;
        }

        return $this;
    }

    public function setMessageFromURL($texturl, $htmlurl = null) {
        if ($texturl !== null) {
            $this->messagetext = file_get_contents($texturl);
        } else {
            $this->messagetext = null;
        }
        if ($htmlurl !== null) {
            $this->messagehtml = file_get_contents($htmlurl);
        } else {
            $this->messagehtml = null;
        }

        return $this;
    }

    public function setMessageCharset($textCharset, $htmlCharset = null) {
        $this->messageTextCharset = $textCharset;
        $this->messageHtmlCharset = $htmlCharset;

        return $this;
    }

    /**
     * Add custom header - this works only with SendRawEmail
     *
     * @param string $header Your custom header
     * @return SimpleEmailServiceMessage $this
     * @link( Restrictions on headers, http://docs.aws.amazon.com/ses/latest/DeveloperGuide/header-fields.html)
     */
    public function addCustomHeader($header) {
        $this->customHeaders[] = $header;

        return $this;
    }

    /**
     * Add email attachment by directly passing the content
     *
     * @param string $name      The name of the file attachment as it will appear in the email
     * @param string $data      The contents of the attachment file
     * @param string $mimeType  Specify custom MIME type
     * @param string $contentId Content ID of the attachment for inclusion in the mail message
     * @return SimpleEmailServiceMessage $this
     */
    public function addAttachmentFromData($name, $data, $mimeType = 'application/octet-stream', $contentId = null) {
        $this->attachments[$name] = array(
            'name' => $name,
            'mimeType' => $mimeType,
            'data' => $data,
            'contentId' => $contentId,
        );

        return $this;
    }

    /**
     * Add email attachment by passing file path
     *
     * @param string $name      The name of the file attachment as it will appear in the email
     * @param string $path      Path to the attachment file
     * @param string $mimeType  Specify custom MIME type
     * @param string $contentId Content ID of the attachment for inclusion in the mail message
     * @return  boolean Status of the operation
     */
    public function addAttachmentFromFile($name, $path, $mimeType = 'application/octet-stream', $contentId = null) {
        if (file_exists($path) && is_file($path) && is_readable($path)) {
            $this->attachments[$name] = array(
                'name' => $name,
                'mimeType' => $mimeType,
                'data' => file_get_contents($path),
                'contentId' => $contentId,
            );
            return true;
        }
        return false;
    }

    /**
     * Get the raw mail message
     *
     * @return string
     */
    public function getRawMessage() {
        $this->addCustomHeader("Force-headers:\t1"); //see weird hack https://github.com/daniel-zahariev/php-aws-ses/issues/8
        $boundary = uniqid(rand(), true);
        $raw_message = join("\n", $this->customHeaders) . "\n";                      
        $raw_message .= 'To: ' . $this->encodeRecipients($this->to) . "\n";
        $raw_message .= 'From: ' . $this->encodeRecipients($this->from) . "\n";
        $raw_message .= 'Reply-To: ' . $this->encodeRecipients($this->replyto) . "\n";

        if (!empty($this->cc)) {
            $raw_message .= 'CC: ' . $this->encodeRecipients($this->cc) . "\n";
        }
        if (!empty($this->bcc)) {
            $raw_message .= 'BCC: ' . $this->encodeRecipients($this->bcc) . "\n";
        }

        if ($this->subject != null && strlen($this->subject) > 0) {
            $raw_message .= 'Subject: =?' . $this->subjectCharset . '?B?' . base64_encode($this->subject) . "?=\n";
        }

        $raw_message .= 'MIME-Version: 1.0' . "\n";
        $raw_message .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\n";
        $raw_message .= "\n--{$boundary}\n";
        $raw_message .= 'Content-type: Multipart/Alternative; boundary="alt-' . $boundary . '"' . "\n";

        if ($this->messagetext != null && strlen($this->messagetext) > 0) {
            $charset = empty($this->messageTextCharset) ? '' : "; charset=\"{$this->messageTextCharset}\"";
            $raw_message .= "\n--alt-{$boundary}\n";
            $raw_message .= 'Content-Type: text/plain' . $charset . "\n\n";
            $raw_message .= $this->messagetext . "\n";
        }

        if ($this->messagehtml != null && strlen($this->messagehtml) > 0) {
            $charset = empty($this->messageHtmlCharset) ? '' : "; charset=\"{$this->messageHtmlCharset}\"";
            $raw_message .= "\n--alt-{$boundary}\n";
            $raw_message .= 'Content-Type: text/html' . $charset . "\n\n";
            $raw_message .= $this->messagehtml . "\n";
        }
        $raw_message .= "\n--alt-{$boundary}--\n";

        foreach ($this->attachments as $attachment) {
            $raw_message .= "\n--{$boundary}\n";
            $raw_message .= 'Content-Type: ' . $attachment['mimeType'] . '; name="' . $attachment['name'] . '"' . "\n";
            $raw_message .= 'Content-Disposition: attachment' . "\n";
            if (!empty($attachment['contentId'])) {
                $raw_message .= 'Content-ID: ' . $attachment['contentId'] . '' . "\n";
            }
            $raw_message .= 'Content-Transfer-Encoding: base64' . "\n";
            $raw_message .= "\n" . chunk_split(base64_encode($attachment['data']), 76, "\n") . "\n";
        }

        $raw_message .= "\n--{$boundary}--\n";
        return base64_encode($raw_message);
    }

    /**
     * Encode recipient with the specified charset in `recipientsCharset`
     *
     * @param  string|array $recipient Single recipient or array of recipients
     * @return string            Encoded recipients joined with comma
     */
    public function encodeRecipients($recipient) {
        if (is_array($recipient)) {
            return join(', ', array_map(array($this, 'encodeRecipients'), $recipient));
        }

        if (preg_match("/(.*)<(.*)>/", $recipient, $regs)) {
            $recipient = '=?' . $this->recipientsCharset . '?B?' . base64_encode($regs[1]) . '?= <' . $regs[2] . '>';
        }

        return $recipient;
    }

    /**
     * Validates whether the message object has sufficient information to submit a request to SES.
     * This does not guarantee the message will arrive, nor that the request will succeed;
     * instead, it makes sure that no required fields are missing.
     *
     * This is used internally before attempting a SendEmail or SendRawEmail request,
     * but it can be used outside of this file if verification is desired.
     * May be useful if e.g. the data is being populated from a form; developers can generally
     * use this function to verify completeness instead of writing custom logic.
     *
     * @return boolean
     */
    public function validate() {
        if (count($this->to) == 0)
            return false;
        if ($this->from == null || strlen($this->from) == 0)
            return false;
        // messages require at least one of: subject, messagetext, messagehtml.
        if (($this->subject == null || strlen($this->subject) == 0) && ($this->messagetext == null || strlen($this->messagetext) == 0) && ($this->messagehtml == null || strlen($this->messagehtml) == 0)) {
            return false;
        }

        return true;
    }

}
