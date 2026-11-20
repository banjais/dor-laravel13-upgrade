<?php

use App\Models\Configuration;
use App\Models\Content;
use App\Models\Home;
use App\Models\Office;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class Helper
{

    /*
    | get last executable query
    |
    | return string
     */

    public static function homeOfficeId()
    {
        return 4;
    }
    public static function get_last_query()
    {
        $queries = DB::getQueryLog();
        $sql     = end($queries);

        if (!empty($sql['bindings'])) {
            $pdo = DB::getPdo();
            foreach ($sql['bindings'] as $binding) {
                $sql['query'] =
                    preg_replace('/\?/', $pdo->quote($binding), $sql['query'], 1);
            }
        }
        return $sql['query'];
    }

    public static function getRequestFormCount()
    {

        return EmailRequest::whereNull('seen_at')->count();
    }

    public static function getNewsTicker($office_id)
    {

        return Home::getTickerOfOffice($office_id);
    }


    /*
     * send email
     * @param string $receiverEmail
     * @param string $subject
     * @param string $content
     * @param string $replyTo
     * @param array $ccBCC
     * @return boolean
     */
    public static function sendEmail($receiverEmail, $subject, $content, $attachments = [], $replyTo = "", $ccBCC = array(), $office_email = null, $office_name = null, $mailer = null, $senderEmailOverride = null, $suppressConfiguredCc = false, $bccRecipients = [])
    {
        $senderEmail = Configuration::getValueFromName('no_reply_email');
        $senderName  = Configuration::getValueFromName('from_name');

        $cc_str  = Configuration::getValueFromName('feedback_cc_to');
        $configuredCc = is_string($cc_str) && trim($cc_str) !== ''
            ? explode(',', $cc_str)
            : [];

        // Contact Form can suppress global automatic CC and use visitor CC only.
        $cc_array = $suppressConfiguredCc
            ? (is_array($ccBCC) ? $ccBCC : [])
            : array_merge($configuredCc, is_array($ccBCC) ? $ccBCC : []);

        //see if email sending is in test mode
        $is_email_test_mode = config('general.email.is_test_mode');
        if ($is_email_test_mode) {
            $senderEmail = config('general.email.test_email_from');

            // In Contact Form test mode, never CC a visitor-supplied address.
            if ($suppressConfiguredCc) {
                $cc_array = [];
                $bccRecipients = [];
            }
        }

        // Test mode must retain its configured sender address.
        // Outside test mode, use the explicitly selected SMTP account's address.
        if (!$is_email_test_mode && $senderEmailOverride) {
            $senderEmail = $senderEmailOverride;
        }

        // dd($senderEmail);

        $sitePath    = Configuration::getValueFromName('absolute_site_path');
        $siteName    = Configuration::getValueFromName('site_name');
        // $office = Office::with('detail')->find(Auth::User()->office_id);


        $data = array(
            'logopath' => 'assets/admin/img/logo.png',
            'content'  => $content,
            'footer'   => ' Copyright ' . date('Y') . ' ',
            'sitepath' => $sitePath,
            'sitename' => $siteName,
        );

        // Normalize recipients to support both single and multiple addresses.
        $receiverEmails = is_array($receiverEmail) ? $receiverEmail : [$receiverEmail];
        $receiverEmailKeys = array_map(function ($email) {
            return strtolower(trim((string) $email));
        }, $receiverEmails);

        // Remove sender and all primary recipients from CC.
        $cc_array = array_values(array_filter($cc_array, function ($cc) use ($senderEmail, $receiverEmailKeys) {
            $cc = strtolower(trim((string) $cc));
            return $cc !== "" &&
                filter_var($cc, FILTER_VALIDATE_EMAIL) &&
                $cc !== strtolower(trim((string) $senderEmail)) &&
                !in_array($cc, $receiverEmailKeys, true);
        }));

        // Normalize and validate BCC recipients; remove duplicates and addresses
        // already present in To, CC, or the effective sender address.
        $effectiveSenderEmail = $office_email ?: $senderEmail;
        $excludedRecipientKeys = array_merge(
            $receiverEmailKeys,
            array_map(function ($email) {
                return strtolower(trim((string) $email));
            }, $cc_array),
            [strtolower(trim((string) $effectiveSenderEmail))]
        );

        $bcc_array = [];
        $seenBccKeys = [];

        foreach ((array) $bccRecipients as $bccEmail) {
            $bccEmail = trim((string) $bccEmail);
            $bccKey = strtolower($bccEmail);

            if (
                $bccEmail !== '' &&
                filter_var($bccEmail, FILTER_VALIDATE_EMAIL) &&
                !in_array($bccKey, $excludedRecipientKeys, true) &&
                !isset($seenBccKeys[$bccKey])
            ) {
                $seenBccKeys[$bccKey] = true;
                $bcc_array[] = $bccEmail;
            }
        }

        try {
            $mailService = $mailer ? Mail::mailer($mailer) : Mail::mailer();

            $sentMessage = $mailService->send('emails.email', $data, function ($message) use ($senderEmail, $senderName, $receiverEmail, $subject, $attachments, $ccBCC, $office_email, $office_name, $cc_array, $bcc_array) {
                if ($office_email) {
                    if (!$office_name) $office_name = $office_email;
                    $message->from($office_email, $office_name);
                } else {
                    $message->from($senderEmail, $senderName);
                }

                $message->to($receiverEmail)->subject($subject);

                if (!empty($cc_array)) {
                    $message->cc($cc_array);
                }

                if (!empty($bcc_array)) {
                    $message->bcc($bcc_array);
                }

                if (count($attachments) > 0) {
                    foreach ($attachments as $attachment) {
                        $message->attach($attachment);
                    }
                }
            });
            if ($sentMessage === null) {
                \Illuminate\Support\Facades\Log::warning("Password reset email returned no sent message.", [
                    "mailer" => $mailer ?: config("mail.default"),
                    "sender_configured" => !empty($senderEmail),
                ]);
                return false;
            }

            return true;
        } catch (RfcComplianceException | TransportExceptionInterface $e) {
            \Illuminate\Support\Facades\Log::warning("Password reset email transport failed.", [
                "exception" => get_class($e),
                "code" => $e->getCode(),
                "message" => preg_replace("/[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,}/i", "[EMAIL HIDDEN]", $e->getMessage()),
            ]);
            return false;
        }
    }

    /**
     * create captcha image dynamically
     *
     * @return string
     */
    public static function create_captcha()
    {
        $builder = new CaptchaBuilder;
        $builder->build();
        $captcha = $builder->inline();
        Session::put('phrase', $builder->getPhrase());
        return $captcha;
    }

    /**
     * Text is limited as provided length
     *
     * @return string
     */
    public static function charcut($text, $max_length = 450, $tail = ' ...')
    {
        $tail_len = strlen($tail);
        if (strlen($text) > $max_length) {
            $tmp_text = substr($text, 0, $max_length - $tail_len);
            if (substr($text, $max_length - $tail_len, 1) == ' ') {
                $text = $tmp_text;
            } else {
                $pos  = strrpos($tmp_text, ' ');
                $text = substr($text, 0, $pos);
            }
            $text = $text . $tail;
        }
        return $text;
    }

    // get subheader in content
    public static function GetSubContentlist($parenturl, $parentId, $counter = "1")
    {
        $sitePath = Configuration::getValueFromName('absolute_site_path');
        $siteName = Configuration::getValueFromName('site_name');

        $menuList = "";
        $newCount = $counter + 1;
        if ($parentId != 0) {
            $listLevHeader = Home::headerContentAllList($parentId);
            //        echo count($listLevHeader)."<br />";
            if (count($listLevHeader) > 0) {

                $menuList .= "<table class='roadproj table-striped table-condensed'><thead><tr><th>SN</th><th>Name</th><th>Download</th></tr></thead><tbody>";
                $subCount = 1;
                foreach ($listLevHeader as $lev2Content) {

                    $listLev2Header = Home::headerContentMenus($lev2Content->id);

                    $menuList .= '<tr><td>' . $subCount . '</td>';

                    $menuList .= '<td>' . $lev2Content->content_heading . '</td>';
                    // $menuList .= '<td>'.$lev2Content->content_attachment.'</td>';

                    if (file_exists("uploads/pages/" . $lev2Content->content_attachment) && $lev2Content->content_attachment != '') {
                        // echo "uploads/page/".$lev2Content->content_attachment;exit;
                        $menuList .= '<td><span><a href="' . $sitePath . 'home/page/force/' . $lev2Content->content_url . '" target="_blank">
											<i class="glyphicon glyphicon-download mr6"></i>Download</a>
										</span></td>';
                        $menuList .= '</tr>';
                    } else {
                        $menuList .= '<td>File Not found.</td>';
                    }

                    $menuList .= '</tr>';
                }

                $subCount++;
                $menuList .= '</tbody></table>';
            }
        }
        return $menuList;
    }

    // get subheader in content
    public static function GetSubContentDoclist($parenturl, $parentId, $counter = "1")
    {
        $sitePath = Configuration::getValueFromName('absolute_site_path');
        $siteName = Configuration::getValueFromName('site_name');

        $menuList = "";
        $newCount = $counter + 1;
        if ($parentId != 0) {
            $listLevHeader = Content::content_alllist_for_attachment($parentId);
            //        echo count($listLevHeader)."<br />";
            if (count($listLevHeader) > 0) {

                $menuList .= "<table class='table table-striped table-condensed'><thead><tr><th>SN</th><th>Name</th><th>Download</th></tr></thead><tbody>";
                $subCount = 1;
                foreach ($listLevHeader as $lev2Content) {

                    $menuList .= '<tr><td>' . $subCount . '</td>';

                    $menuList .= '<td>' . $lev2Content->document_name . '</td>';
                    // $menuList .= '<td>'.$lev2Content->content_attachment.'</td>';

                    if (file_exists("uploads/pages/" . $lev2Content->document_attachment) && $lev2Content->document_attachment != '') {
                        // echo "uploads/page/".$lev2Content->content_attachment;exit;
                        $menuList .= '<td><span><a href="' . $sitePath . 'home/page/force/' . $lev2Content->id . '" target="_blank">
											<i class="glyphicon glyphicon-download mr6"></i>Download</a>
										</span></td>';
                        $menuList .= '</tr>';
                    } else {
                        $menuList .= '<td>File Not found.</td>';
                    }

                    $menuList .= '</tr>';
                    $subCount++;
                }

                $menuList .= '</tbody></table>';
            }
        }
        return $menuList;
    }

    /**
     * Normalize CMS footer links for local and production environments.
     * Same-site absolute URLs become relative; external URLs remain unchanged.
     */
    public static function normalizeFooterLinks($html)
    {
        if (!is_string($html) || $html === '') {
            return $html;
        }

        return preg_replace_callback(
            '~(\bhref\s*=\s*)(["\'])(.*?)\2~is',
            static function ($matches) {
                $url = html_entity_decode(
                    $matches[3],
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $parts = parse_url($url);

                if ($parts === false || empty($parts['host'])) {
                    return $matches[0];
                }

                $host = strtolower(rtrim($parts['host'], '.'));

                if (!in_array($host, ['dor.gov.np', 'www.dor.gov.np'], true)) {
                    return $matches[0];
                }

                $normalized = $parts['path'] ?? '/';

                if (isset($parts['query'])) {
                    $normalized .= '?' . $parts['query'];
                }

                if (isset($parts['fragment'])) {
                    $normalized .= '#' . $parts['fragment'];
                }

                $normalized = htmlspecialchars(
                    $normalized,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                );

                return $matches[1] . $matches[2] . $normalized . $matches[2];
            },
            $html
        );
    }

    public static function getNameOftext($key, $lang)
    {
        info($key.' - '.$lang);
        return config('lang.' . $key)[$lang];
    }
}
