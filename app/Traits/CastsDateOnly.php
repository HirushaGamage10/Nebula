<?php

namespace App\Traits;

/**
 * Fixes an off-by-one-day bug: Eloquent serializes 'date' casts to JSON via
 * Carbon::toJSON(), which converts the local date to UTC and can shift the
 * calendar day. This re-formats date-cast attributes as plain 'Y-m-d' strings
 * (no time/timezone) so the saved date is never shifted when displayed.
 */
trait CastsDateOnly
{
    public function toArray()
    {
        $array = parent::toArray();

        foreach ($this->getCasts() as $key => $type) {
            if (!in_array($type, ['date', 'immutable_date'], true) || !array_key_exists($key, $array)) {
                continue;
            }

            $value = $this->getAttribute($key);
            $array[$key] = $value ? $value->format('Y-m-d') : null;
        }

        return $array;
    }
}
