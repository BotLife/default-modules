<?php

namespace Botlife\Module\GoogleApps\Command;

use Ircbot\Type\MessageCommand;

class GoogleDocsInfo extends \Botlife\Command\ACommand
{

    public $regex  = array(
        '/^https\:\/\/docs\.google\.com\/spreadsheet\/ccc\?(.*)?key=(?P<id>[A-Za-z0-9_-]+)/i',
        '/^https\:\/\/docs\.google\.com\/(document|folder|file|presentation)\/d\/(?P<id>[A-Za-z0-9_-]+)\/edit/i',
        '/^https\:\/\/docs\.google\.com\/open?(.*)?id=(?P<id>[A-Za-z0-9_-]+)/i',
    );
    public $action = 'run';
    public $code   = 'google-drive';
    
    public $responseType    = self::RESPONSE_PUBLIC;
    
    public function run(MessageCommand $event)
    {
        $this->detectResponseType($event->message, $event->target);
        $id = $event->matches['id'];
        $url        = sprintf(
            'https://www.googleapis.com/drive/v1/files/%s?', $id
        );
        $key = \Botlife\Application\Config::getOption('google.key');
        if (!$key) {
            return false;
        }
        $parameters = array(
            'key' => $key,
        );
        $url .= http_build_query($parameters);
        $json = \DataGetter::getData('file-content', $url);
        if (!$json) {
            return;
        }
        $data = json_decode($json);
        if (isset($data->error)) {
            return;
        }
        $this->respondWithInformation($this->_createResponseData($data));
    }
    
    public function getDocumentType($mimeType)
    {
        switch ($mimeType) {
            case 'application/vnd.google-apps.document':
                return 'Document';
            case 'application/vnd.google-apps.spreadsheet':
                return 'Spreadsheet';
            case 'application/vnd.google-apps.folder':
                return 'Folder';
            case 'application/vnd.google-apps.presentation':
                return 'Presentation';
            case 'application/vnd.openxmlformats-officedocument.'
                . 'wordprocessingml.document':
                return 'Microsoft Office Word document';
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
                return 'Microsoft Office Word template';
            case 'application/x-msdos-program':
                return 'Windows executable';
            case 'image/jpeg':
                return 'JPEG image';
            case 'text/plain':
                return 'Text file';
            default:
                print_r($mimeType);
                return 'Unknown';
        }
    }
    
    public function pluralize($count, $text) 
    { 
        return $count . (($count == 1) ? (" $text") : (" ${text}s"));
    }

    public function ago($oldDate, $newDate = null)
    {
        if (!$newDate) {
            $newDate = new \DateTime('now');
        }
        $interval = $newDate->diff($oldDate);
        $suffix = ($interval->invert ? ' ago' : '');
        if ($v = $interval->y >= 1) {
            return $this->pluralize($interval->y, 'year') . $suffix;
        }
        if ($v = $interval->m >= 1) {
            return $this->pluralize($interval->m, 'month') . $suffix;
        }
        if ($v = $interval->d >= 1) {
            return $this->pluralize($interval->d, 'day') . $suffix;
        }
        if ($v = $interval->h >= 1) {
            return $this->pluralize($interval->h, 'hour') . $suffix;
        }
        if ($v = $interval->i >= 1) {
            return $this->pluralize($interval->i, 'minute') . $suffix;
        }
        return $this->pluralize($interval->s, 'second') . $suffix;
    }
    
    private function _createResponseData($data)
    {
        $math   = new \Botlife\Utility\Math;
        $math->units = array('kB', 'MB', 'GB', 'TB');
        $response = array(
            'Title' => $data->title,
            'Type'  => $this->getDocumentType($data->mimeType),
        );
        $dates = new \StdClass;
        list($dates->created, $dates->modified) = array(
            new \DateTime($data->createdDate),
            new \DateTime($data->modifiedDate),
        );
        if (isset($data->fileSize)) {
            $response['File size'] = $math->alphaRound($data->fileSize);
        }
        $response['Created'] = $dates->created->format('Y-m-d H:i:s');
        $response['Modified'] = array(
            $dates->modified->format('Y-m-d H:i:s'),
            array(
                $this->ago($dates->created),
            ),
        );
        if (isset($data->description)) {
            $length = 100;
            if (strlen($data->description) > $length) {
                $data->description = substr(
                    $data->description, 0, $length
                ) . '...';
            }
            $response['Description'] = $data->description;
        }
        return $response;
    }

}
    
