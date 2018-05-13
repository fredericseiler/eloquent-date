<?php

namespace Seiler\EloquentDate;

use Jenssegers\Date\Date;

/**
 * Class EloquentDate
 *
 * @package Seiler\EloquentDate
 */
trait EloquentDate
{
    /**
     * 
     * Get a fresh timestamp for the model. (Concerns\HasTimestamps)
     *
     * @return \Jenssegers\Date\Date
     */
    public function freshTimestamp()
    {
        return new Date;
    }

    /**
     * Return a timestamp as DateTime object. (Concerns\HasAttributes)
     *
     * @param  mixed $value
     * @return \Jenssegers\Date\Date
     */
    protected function asDateTime($value)
    {
        // If this value is already a Date instance, we shall just return it as is.
        // This prevents us having to reinstantiate a Date instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof Date) {
            return $value;
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTimeInterface) {
            return new Date(
                $value->format('Y-m-d H:i:s.u'), $value->getTimeZone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Date object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Date instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Datized conversion.
        if ($this->isStandardDateFormat($value)) {
            return Date::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Date object
        // that is returned back out to the developers after we convert it here.
        return Date::createFromFormat(
            str_replace('.v', '.u', $this->getDateFormat()), $value
        );
    }
}
