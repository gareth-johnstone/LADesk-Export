<?php
/**
 * LADesk Export - Export
 * To use, access the file in your browser with a GET variable defining the offset
 * This script will export 1000 tickets at a time, so the "start" should be in multiples of 1000
 * eg: export.php?start=0
 * eg: export.php?start=1000
 * eg: export.php?start=2000
 *
 * @category  LADesk
 * @package   LADesk Export
 * @author    Gareth Johnstone <gareth.johnstone@iqx.co.uk>
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      http://github.com/gareth-johnstone/LADesk-Export 
 * @version   1.0.0
 */

//URL TO YOUR LIVEAGENT INSTALLATION
const LIVEAGENT_API_URL = 'https://<YOUR-LIVE-AGENT-URL>.ladesk.com/api/';

//YOUR API KEY YOU CAN FIND IN MENU Configuration -> System -> Api
const API_KEY = '';

$startAt = $_GET['start'];

function sendRequest($apiCommand, $v3=false, $params = array()) {
    $params['apikey'] = API_KEY;
    $paramsStr = '';
    foreach ($params as $param => $value) {
        $paramsStr .= $param . '=' . urlencode($value) . '&';
    }
    $ch = curl_init();

    if ($v3) {
        curl_setopt($ch, CURLOPT_URL, LIVEAGENT_API_URL . "v3/" . $apiCommand . '?' . rtrim($paramsStr, '&'));
    } else {
        curl_setopt($ch, CURLOPT_URL, LIVEAGENT_API_URL . $apiCommand . '?' . rtrim($paramsStr, '&'));
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($ch);
    if ($curl_response === false) {
        $info = curl_error($ch);
        curl_close($ch);
        die("error occured during curl exec. Additioanl info: " . var_export($info));
    }
    curl_close($ch);
    return json_decode($curl_response);
}

function fixCSVParsing($in){
    $in = str_replace('<o:p>', "", $in);
    $in = str_replace('</o:p>', "", $in);
    $in = str_replace('"', '""', $in);
    $in = str_replace('\'', '\'\'', $in);
    $in = str_replace(',', '', $in);
    $in = preg_replace( "/\r|\n/", "", $in );
    $in = trim($in);
    return $in;
}

$fp = fopen('tickets_'.$startAt.'.csv', 'w');

$conversations = sendRequest('conversations', false, array(
        'limit' => '1000',
        'offset' => $startAt
    ));

if (isset($conversations->response->conversations)) {
    foreach ($conversations->response->conversations as $conversation) {
        $conversationMessages = sendRequest('conversations/' . $conversation->conversationid . '/messages');
        if (isset($conversationMessages->response->groups)) {
            foreach ($conversationMessages->response->groups as $group) {
                $messagesStr = '';
                if (isset($group->messages)) {
                    foreach ($group->messages as $message) {
                        $messagesStr .= $message->userid . ' (' . $message->datecreated . '): ' . $message->message . "\n";
                    }
                }
                $string = array(
                        "\"" . $conversation->conversationid . "\"",
                        "\"" . $conversation->datecreated . "\"",
                        "\"" . $conversation->departmentname . "\"",
                        "\"" . $conversation->ownername . "\"",
                        "\"" . $conversation->owneremail . "\"",
                        "\"" . $conversation->publicurlcode . "\"",
                        "\"" . $conversation->tags . "\"",
                        "\"" . $conversation->status . "\"",
                        "\"" . $group->datecreated . "\"",
                        "\"" . $group->datefinished . "\"",
                        "\"" . $conversation->subject . "\"",
                        "\"" . fixCSVParsing($messagesStr) . "\"",
                        "\r\n"
                );
                fwrite($fp, implode(",", $string));
            }
        }
    }
}
fclose($fp);
?>