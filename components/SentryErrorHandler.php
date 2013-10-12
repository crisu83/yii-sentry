<?php
/**
 * SentryErrorHandler class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-sentry.components
 */

/**
 * Error handler that allows for sending errors to Sentry.
 */
class SentryErrorHandler extends CErrorHandler
{
    /**
     * @var string component ID for the sentry client.
     */
    public $sentryClientID = 'sentry';

    /**
     * Initializes the error handler.
     */
    public function init()
    {
        parent::init();
        Yii::app()->attachEventHandler('onEndRequest', array($this, 'onShutdown'));
    }

    /**
     * Invoked on shutdown to attempt to capture any unhandled errors.
     */
    public function onShutdown()
    {
        $error = error_get_last();
        if ($error !== null) {
            $errors = array(
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_CORE_WARNING,
                E_COMPILE_ERROR,
                E_COMPILE_WARNING,
                E_STRICT
            );
            if (in_array($error['type'], $errors)) {
                $this->getSentryClient()->captureException(
                    $this->createErrorException($error['message'], $error['type'], $error['file'], $error['line'])
                );
            }
        }
    }

    /**
     * Handles the PHP error.
     * @param CErrorEvent $event the PHP error event
     */
    protected function handleError($event)
    {
        if (error_reporting() & $event->code) {
            $this->getSentryClient()->captureException(
                $this->createErrorException($event->message, $event->code, $event->file, $event->line)
            );
        }
        parent::handleError($event);
    }

    /**
     * Handles the exception.
     * @param Exception $exception the exception captured.
     */
    protected function handleException($exception)
    {
        $this->getSentryClient()->captureException($exception);
        parent::handleException($exception);
    }

    /**
     * Creates an error exception.
     * @param string $message error message.
     * @param int $code error code.
     * @param string $file file in which the error occurred.
     * @param int $line line number on which the error occurred.
     * @return ErrorException exception instance.
     */
    protected function createErrorException($message, $code, $file, $line)
    {
        return new ErrorException($message, $code, 0/* will be resolved */, $file, $line);
    }

    /**
     * Returns the Sentry client component.
     * @return SentryClient client instance.
     * @throws CException if the component id is invalid.
     */
    public function getSentryClient()
    {
        if (!Yii::app()->hasComponent($this->sentryClientID)) {
            throw new CException(sprintf('SentryErrorHandler.componentID "%s" is invalid.', $this->sentryClientID));
        }
        return Yii::app()->getComponent($this->sentryClientID);
    }
} 