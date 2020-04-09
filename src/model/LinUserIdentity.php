<?php


namespace LinCmsTp5\admin\model;


use LinCmsTp5\admin\exception\user\UserException;
use think\Exception;
use think\Model;
use think\model\concern\SoftDelete;

class LinUserIdentity extends Model
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = 'datetime';
    protected $hidden = ['delete_time', 'update_time'];

    const TYPE = 'USERNAME_PASSWORD';

    /**
     * @param $username
     * @param $password
     * @return array|\PDOStatement|string|Model
     * @throws UserException
     */
    public static function verify($username, $password)
    {
        try {
            $user = self::where('identity_type', self::TYPE)
                ->where('identifier', $username)
                ->findOrFail();
        } catch (Exception $ex) {
            throw new UserException();
        }

        if (!self::checkPassword($user->credential, $password)) {
            throw new UserException([
                'code' => 400,
                'msg' => '用户名或密码错误',
                'error_code' => 10031
            ]);
        }

        return $user->hidden(['credential']);

    }


    private static function checkPassword($md5Password, $password)
    {
        return $md5Password === md5($password);
    }
}