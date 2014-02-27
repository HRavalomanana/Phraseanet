<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2014 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Alchemy\Phrasea\Notification\Receiver;
use Alchemy\Phrasea\Notification\Mail\MailInfoBridgeUploadFailed;

class eventsmanager_notify_bridgeuploadfail extends eventsmanager_notifyAbstract
{
    /**
     *
     * @var string
     */
    public $events = ['__BRIDGE_UPLOAD_FAIL__'];

    /**
     *
     * @return string
     */
    public function icon_url()
    {
        return '/skins/icons/user.png';
    }

    /**
     *
     * @param  string        $event
     * @param  Array         $params
     * @param  mixed content $object
     * @return Void
     */
    public function fire($event, $params, &$object)
    {
        $default = [
            'usr_id'     => null
            , 'reason'     => ''
            , 'account_id' => null
            , 'base_id'    => null
            , 'record_id'  => null
        ];

        $params = array_merge($default, $params);

        $dom_xml = new DOMDocument('1.0', 'UTF-8');
        $dom_xml->preserveWhiteSpace = false;
        $dom_xml->formatOutput = true;

        $root = $dom_xml->createElement('datas');

        $reason = $dom_xml->createElement('reason');
        $account_id = $dom_xml->createElement('account_id');
        $sbas_id = $dom_xml->createElement('sbas_id');
        $record_id = $dom_xml->createElement('record_id');

        $reason->appendChild($dom_xml->createTextNode($params['reason']));
        $account_id->appendChild($dom_xml->createTextNode($params['account_id']));
        $sbas_id->appendChild($dom_xml->createTextNode($params['sbas_id']));
        $record_id->appendChild($dom_xml->createTextNode($params['record_id']));

        $root->appendChild($reason);
        $root->appendChild($account_id);
        $root->appendChild($sbas_id);
        $root->appendChild($record_id);

        $dom_xml->appendChild($root);

        $datas = $dom_xml->saveXml();

        $mailed = false;

        if ($this->shouldSendNotificationFor($params['usr_id'])) {

            $readyToSend = false;
            try {
                $user = $this->app['repo.users']->find($params['usr_id']);
                $account = Bridge_Account::load_account($this->app, $params['account_id']);
                $receiver = Receiver::fromUser($user);
                $readyToSend = true;
            } catch (\Exception $e) {

            }

            if ($readyToSend) {
                /** ADD MAIL TEST */
                $mail = MailInfoBridgeUploadFailed::create($this->app, $receiver);
                $mail->setAdapter($account->get_api()->get_connector()->get_name());
                $mail->setReason($params['reason']);
                $this->app['notification.deliverer']->deliver($mail);
                $mailed = true;
            }
        }

        $this->broker->notify($params['usr_id'], __CLASS__, $datas, $mailed);

        return;
    }

    /**
     *
     * @param  Array   $datas
     * @param  boolean $unread
     * @return Array
     */
    public function datas($datas, $unread)
    {
        $sx = simplexml_load_string($datas);

        $reason = (string) $sx->reason;
        $account_id = (int) $sx->account_id;
        $sbas_id = (int) $sx->sbas_id;
        $rid = (int) $sx->record_id;

        try {
            $account = Bridge_Account::load_account($this->app, $account_id);
            $record = new record_adapter($this->app, $sbas_id, $rid);
        } catch (\Exception $e) {
            return [];
        }

        $ret = [
            'text'  => $this->app->trans("L'upload concernant le record %title% sur le compte %bridge_name% a echoue pour les raisons suivantes : %reason%", [
                '%title%' => $record->get_title(),
                '%bridge_name%' => $account->get_api()->get_connector()->get_name(),
                '%reason%' => $reason
            ])
            , 'class' => ''
        ];

        return $ret;
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->app->trans('Bridge upload fail');
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->app->trans('Recevoir des notifications lorsqu\'un upload echoue sur un bridge');
    }

    /**
     * @param integer $usr_id The id of the user to check
     *
     * @return boolean
     */
    public function is_available($usr_id)
    {
        return true;
    }
}
