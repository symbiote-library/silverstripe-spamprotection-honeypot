<?php namespace StudioBonito\SilverStripe\SpamProtection\Honeypot\FormField;

use i18n;
use FormField;

/**
 * HoneypotField.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\SpamProtection\Honeypot\SpamProtectorField
 */
class HoneypotField extends \HiddenField
{
    /**
     * The number of seconds before you can submit a valid request.
     *
     * @var int
     */
    private static $time_limit = 5;

    /**
     * Reject the field if the honeypot has been filled or if the form has been submitted to quickly.
     *
     * @param $validator
     *
     * @return bool
     */
    public function validate($validator)
    {
        $timeLimit = $this->stat('time_limit');

        $timestamp = $this->getForm()->getController()->getRequest()->postVar($this->getName() . '_Timestamp');

        if (!empty($this->value) || ($timeLimit > 0 && ($timestamp + $timeLimit) > time())) {
            $validator->validationError(
                $this->name,
                i18n::_t(
                    'HoneypotField.SPAM',
                    'Your submission has been rejected because it was treated as spam.'
                ),
                'error'
            );

            return false;
        }

        return true;
    }

    /**
     * @codeCoverageIgnoreStart
     */

    /**
     * Override the Type to remove the class namespace.
     *
     * @return string
     */
    public function Type()
    {
        return 'honeypotspamprotector';
    }

    /**
     * Override the Field to add the Captcha and Timestamp fields.
     *
     * @param array $properties
     *
     * @return string
     */
    public function Field($properties = array())
    {
        return $this->createHoneypotField() . $this->createTimestampField();
    }

    /**
     * Create the Captcha Field.
     *
     * @return string
     */
    protected function createHoneypotField()
    {
        return FormField::create_tag(
            'input',
            array(
                'type'      => 'text',
                'id'        => $this->ID(),
                'name'      => $this->getName(),
                'value'     => $this->Value(),
                'style'     => 'display:none!important'
            )
        );
    }

    /**
     * Create the Timestamp Field.
     *
     * @return string
     */
    protected function createTimestampField()
    {
        return FormField::create_tag(
            'input',
            array(
                'type'      => 'text',
                'id'        => $this->ID() . '_Timestamp',
                'name'      => $this->getName() . '_Timestamp',
                'value'     => time(),
                'style'     => 'display:none!important'
            )
        );
    }

    /**
     * @codeCoverageIgnoreEnd
     */
} 