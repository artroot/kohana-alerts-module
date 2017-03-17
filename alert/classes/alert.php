<?php


/**
 * Created by PhpStorm.
 * User: art
 * Date: 10.03.17
 * Time: 10:45
 */
class Alert
{

    const _DEACTIVATE_DATE                  = 'Дата припинення послуги';
    const _DEVICE                           = 'Пристрій';
    const _ADDED                            = 'додано';
    const _DELETED                          = 'видалено';
    const _ACTIVATED                        = 'увімкнено';
    const _DEACTIVATED                      = 'вимкнено';

    const _ERROR_DEFAULT                    = 'Помилка';
    const _ERROR_DEACTIVATE_POSSIBLY_ONLY   = 'Припинення послуги можливе лише';
    const _ERROR_SERVICE_ALREADY_ACTIVE     = 'Послуга вже активована';
    const _ERROR_SERVICE_INACTIVE           = 'Послуга вимкнена';
    const _ERROR_NOT_ENOUGH_MONEY           = 'Недостатньо коштів';
    const _ERROR_TRY_LATER                  = ', спробуйте пізніше, або зверніться до технічної підтримки';
    const _ERROR_SCHEDULE_EXISTS            = 'Розклад вже існує';
    const _ERROR_DEVICE_EXISTS              = 'Це пристрій вже використовується, будь ласка додайте інший пристрій';
    const _ERROR_CODE_NOT_NUMBER            = 'Код повинен складатися з чотирьох цифр';
    const _ERROR_INVALID_MAC                = 'Мак-адреса повинна складатися з дванадцяти символів без знаків двокрапки (:) або тире (-)';
    const _ERROR_DEVICE_ADDED_FAIL          = 'Неможливо додати пристрій';
    const _ERROR_DEVICE_DEL_FAIL            = 'Неможливо видалити пристрій';
    const _ERROR_DEVICE_ACTIVATE_FAIL       = 'Неможливо увімкнути пристрій';
    const _ERROR_DEVICE_DEACTIVATE_FAIL     = 'Неможливо вимкнути пристрій';
    const _ERROR_INVALID_DEACTIVATE_DATE    = 'Дата припинення послуги не відповідає правильному формату';
    const _ERROR_SERVICE_DEACTIVATE_FAIL    = 'Припинення послуги неможливе';
    const _ERROR_CHANNEL_LIST_EMPTY         = 'Список каналів відсутній, спробуйте пізніше';

    const _SUCCESS_SERVICE_ACTIVATED        = 'Послугу активовано';
    const _SUCCESS_SERVICE_ON               = 'Послугу підключено';
    const _SUCCESS_SERVICE_DEACTIVATED      = 'Послугу відключено';
    const _SUCCESS_ACT_ADDED_TO_SCHEDULE    = 'Дiю внесено в розклад';
    const _SUCCESS_SERVICE_WILL_DEACTIVATE  = 'Послуга буде відключена';

    const _WARNING_ATTENTION                = 'Зверніть увагу!';

    const _INFO_OPERATION_SCHEDULE_OFF      = 'В розкладі операція Вимкнення';

    private static $alert_class = array(
        'error'     => 'alert-error',
        'invalid'   => 'alert-error',
        'success'   => 'alert-success',
        'warning'   => 'alert-warning',

    );

    private static $out_types = array(
        'result',
        'error',
    );

    /**
     * Magic method will call if called method does not exist.
     * @param $name
     * @param $arguments
     * @return string // default message
     */
    public static function __callStatic($name, $arguments)
    {
        return self::_ERROR_DEFAULT . self::_ERROR_TRY_LATER;
    }

    /**
     * Out stack of alert type results to one message result
     * @param array $messages
     * @param string $outType
     * @return array
     */
    public static function factory($messages = array(), $outType = 'result')
    {
        if (empty($messages) or !in_array($outType, self::$out_types)) return self::error(self::_ERROR_DEFAULT, self::_ERROR_TRY_LATER);

        $outMessages['success'] = $outMessages['error'] = $outMessages['warning'] = $outMessages['other'] = array();

        foreach ($messages as $message){
            if (isset($message['alert'])){
                switch ($message['alert']){
                    case 'success' :
                        $outMessages['success'][] = $message['result'];
                    break;
                    case 'error' :
                        $outMessages['error'][] = $message['error'];
                    break;
                    case 'warning' :
                        $outMessages['warning'][] = $message['result'];
                    break;
                    default :
                        if ($message['result'] !== false) $outMessages['other'][] = $message['result'];
                        else $outMessages['other'][] = $message['error'];
                    break;

                }
            }
        }

        switch ($outType){
            case 'result':
                return array(
                    'alert'  => 'success',
                    'result' => implode("\n", $outMessages['success']) . implode("\n", $outMessages['error']) . implode("\n", $outMessages['warning']) . implode("\n", $outMessages['other']),
                    'error'  => false,
                );
            break;
            case 'error':
                return array(
                    'alert'  => 'success',
                    'result'  => false,
                    'error' => implode("\n", $outMessages['error']) . implode("\n", $outMessages['error']) . implode("\n", $outMessages['warning']) . implode("\n", $outMessages['other']),
                );
            break;
        }

    }

    /**
     * Out success message
     * @param bool $head
     * @param string|array $message
     * @return array
     */
    public static function success($head = false, $message = null)
    {
        $outMessage = ($head !== false ? '<strong>' . $head . '</strong>' : null);

        if (is_array($message)) foreach ($message as $text) $outMessage .= ' ' . $text;
        else $outMessage .= ($head !== false ? ' ' . $message : $message);

        return array(
            'alert'  => 'success',
            'result' => '<p class="' . self::$alert_class['success'] . '">' . $outMessage . '</p>',
            'error'  => false,
        );
    }

    /**
     * Out error message
     * @param bool $head
     * @param string|array $message
     * @return array
     */
    public static function error($head = false, $message = null)
    {
        $outMessage = ($head !== false ? '<strong>' . $head . '</strong>' : null);

        if (is_array($message)) foreach ($message as $text) $outMessage .= ' ' . $text;
        else $outMessage .= ($head !== false ? ' ' . $message : $message);

        return array(
            'alert'  => 'error',
            'result' => false,
            'error'  => '<p class="' . self::$alert_class['error'] . '">' . $outMessage . '</p>',
        );
    }

    /**
     * Out warning message
     * @param bool $head
     * @param string|array $message
     * @return array
     */
    public static function warning($head = false, $message = null)
    {
        $outMessage = ($head !== false ? '<strong>' . $head . '</strong>' : null);

        if (is_array($message)) foreach ($message as $text) $outMessage .= ' ' . $text;
        else $outMessage .= ($head !== false ? ' ' . $message : $message);

        return array(
            'alert'  => 'warning',
            'result' => '<p class="' . self::$alert_class['warning'] . '">' . $outMessage . '</p>',
            'error'  => false,
        );
    }

    /**
     * Out validation errors
     * @param null|array $message
     * @return array
     */
    public static function invalid($message = null)
    {
        $outMessage = null;

        if (is_array($message)) foreach ($message as $text) $outMessage .= '<p class="alert-error">' . $text . '</p>';
        else $outMessage .= '<p class="' . self::$alert_class['invalid'] . '">' . $message . '</p>';

        return array(
            'alert'  => 'error',
            'result' => false,
            'error'  => $outMessage,
        );
    }

    /**
     * Out converted one of methods result array to string
     * @param array $alert
     * @return string
     */
    public static function toText($alert = array())
    {
        if (empty($alert)) return self::defaultMessage();

        if(isset($alert['error']) and $alert['error'] !== false) return $alert['error'];
        elseif(isset($alert['result']) and $alert['result'] !== false) return $alert['result'];
        else return self::defaultMessage();
    }


}