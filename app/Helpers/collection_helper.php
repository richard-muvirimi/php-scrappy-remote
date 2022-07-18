<?php

/**
 * Collection helper functions
 */

 use function _\head as array_first;

/**
 * Ensure we get an array
 *
 * @param array|string|integer $value Maybe array value.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return array
 */
function array_maybe($value):array
{
	return is_array($value) ? $value : [$value];
}

/**
 * Gets the first filtered element from an array
 *
 * @param array    $values    Array to filter.
 * @param callable $predicate Function to filter by.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed
 */
function array_filter_first(array $values, callable $predicate)
{
	return array_first(array_filter($values, $predicate));
}
