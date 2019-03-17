<?php
namespace humhub\modules\user\authclient;
use yii\authclient\OAuth2;
use humhub\modules\user\models\Auth;

class IYO extends OAuth2
{
    public $authUrl    = 'https://itsyou.online/v1/oauth/authorize';
    public $tokenUrl   = 'https://itsyou.online/v1/oauth/access_token';
    public $apiBaseUrl = 'https://itsyou.online/api';
    public $scope      = 'user:name,user:email';

    protected function initUserAttributes() {
        $userName = $this->getAccessToken()->getParams()['info']['username'];
        $userInfo = $this->api('users/'.$userName.'/info', 'GET');
        $attributes = [
            "username" => $userInfo['username'],
            "firstname" => $userInfo['firstname'],
            "lastname" => $userInfo['lastname'],
            "email" => $userInfo['emailaddresses'][0]['emailaddress'],
            "id" => $userInfo['username']
        ];

	$systemUsers = (new \yii\db\Query())
                ->select(['id'])
                ->from('user')
                ->where(['email' => $attributes['email']])
                ->limit(1)
                ->all();

        if (count($systemUsers) == 1){

                 $iyoUsers = (new \yii\db\Query())
                        ->select(['user_id'])
                        ->from('user_auth')
                        ->where([ 'user_id' => $systemUsers[0]['id']])
                        ->limit(1)
                        ->all();

                 if (count($iyoUsers) == 0){
                         $o = new Auth;
                         $o -> user_id =  $systemUsers[0]['id'];
                         $o -> source_id = $attributes['username'];
                         $o -> source = 'itsyouonline';
                         $o -> save();


                 }


        }

        return $attributes;
    }

    public function fetchAccessToken($authCode, array $params = []) {
        // Decode the return url to be the same as sent to itsyou.online in the first request
        $extraParams = [
            'redirect_uri' => urldecode($this->getReturnUrl()),
        ];
        return parent::fetchAccessToken($authCode, array_merge($params, $extraParams));
    }

    protected function defaultName() {
        return 'itsyouonline';
    }

    protected function defaultTitle() {
        return 'itsyouonline';
    }
}
