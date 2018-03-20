<?php

namespace TemplateMonster\ThemeOptions\Plugin;

class DateFormat
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context, array $data = []
    ) {
        $this->_localeDate = $context->getLocaleDate();

    }

    public function aroundformatDate($subject, \Closure $proceed,
                                     $date = null,
                                     $format = \IntlDateFormatter::SHORT,
                                     $showTime = false,
                                     $timezone = null)
    {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            'ro_RO',
            $timezone
        );
    }

}