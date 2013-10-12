<?php
/**
 * SentryLogRoute class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-sentry.components
 */

/**
 * Log route that allows for sending messages to Sentry.
 */
class SentryLogRoute extends CLogRoute
{
    /**
     * @var string component ID for the sentry client.
     */
    public $sentryClientID = 'sentry';

    /**
     * Processes log messages and sends them to specific destination.
     * Derived child classes must implement this method.
     * @param array $logs list of messages. Each array element represents one message
     * with the following structure:
     * array(
     *   [0] => message (string)
     *   [1] => level (string)
     *   [2] => category (string)
     *   [3] => timestamp (float, obtained by microtime(true)
     * );
     */
    protected function processLogs($logs)
    {
        foreach ($logs as $log) {
            $this->getSentryClient()->captureMessage(
                $log[0],
                array(),
                array(
                    'extra' => array(
                        'message' => $log[0],
                        'level' => $log[1],
                        'category' => $log[2],
                        'log_time' => date('Y-m-d H:i:s', $log[3]),
                    ),
                )
            );
        }
    }

    /**
     * Returns the Sentry client component.
     * @return SentryClient client instance.
     * @throws CException if the component id is invalid.
     */
    public function getSentryClient()
    {
        if (!Yii::app()->hasComponent($this->sentryClientID)) {
            throw new CException(sprintf('SentryLogRoute.componentID "%s" is invalid.', $this->sentryClientID));
        }
        return Yii::app()->getComponent($this->sentryClientID);
    }
}