<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Email;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    public function actionData()
    {
        // array of possible top-level domains
        $tlds = array("com", "net", "gov", "org", "edu", "biz", "info");

        // string of possible characters
        $domens = ['gmail', 'inbox', 'mail', 'box'];

        $names = ['serg', 'park', 'man', 'home', 'user', 'admin', 'superman', 'money', 'star', 'wagner', 'irina', 'yana', 'kirill', 'black', 'alice', 'bob', 'nikolas', 'colt', 'york'];

        foreach ($names as $name) {
            foreach ($domens as $domen) {
                foreach ($tlds as $tld) {
                    $model = new Email([
                        'email' => $name. '@' . $domen . '.' . $tld,
                    ]);
                    $model->save();
                }
            }
        }
    }
}
