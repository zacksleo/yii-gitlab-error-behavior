<?php

namespace zacksleo\yii\gitlab\behaviors;

/**
 * Class ErrorBehavior
 * @package zacksleo\yii2\gitlab\behaviors\ErrorBehavior
 * @property string $apiRoot
 * @property string $privateToken
 * @property string $projectName
 */
class ErrorBehavior extends \CBehavior
{
    public $apiRoot;
    public $privateToken;
    public $projectName;

    public function events()
    {
        return [
            'onError' => 'onError'
        ];
    }

    /**
     * @param $event
     * @return bool|mixed
     */
    public function onError($event)
    {
        $error = \Yii::app()->errorHandler->error;
        if (!in_array($error['code'], ['404', '400'])) {
            $projectId = $this->getProjectId();
            if (empty($projectId)) {
                return true;
            }
            $url = $this->apiRoot . '/projects/' . $projectId . '/issues';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'PRIVATE-TOKEN: ' . $this->privateToken,
            ));
            $description = '';
            if (!empty(\Yii::app()->request->getUserHostAddress())) {
                $description .= '<blockquote>IP: ' . \Yii::app()->request->getUserHostAddress() . '</blockquote>';
            }
            if (!empty(\Yii::app()->request->getUrlReferrer())) {
                $description .= '<blockquote>Refer:' . \Yii::app()->request->getUrlReferrer() . '</blockquote>';
            }
            if (YII_DEBUG) {
                $description .= '<blockquote>X-Debug-Tag:' . time() . '</blockquote>';
            }
            $content = htmlspecialchars(
                \VarDumper::dumpAsString($_REQUEST),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8',
                true
            );
            $description .= '<blockquote>' . $content . '</blockquote>';
            $description .= '<blockquote>URL: ' . \Yii::app()->request->url;
            $description .= '</blockquote><br/><pre>' . $error['trace'] . '</pre>';
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                [
                    'title' => $error['message'],
                    'description' => $description,
                    'labels' => 'bug',
                ]
            );
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    /**
     * @return bool|integer
     */
    private function getProjectId()
    {
        $url = $this->apiRoot . '/projects/' . urlencode($this->projectName);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'PRIVATE-TOKEN: ' . $this->privateToken,
        ));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode >= 200 && $httpCode < 300) {
            $project = json_decode($data, true);
            return isset($project['id']) ? $project['id'] : false;
        }
        return false;
    }
}
