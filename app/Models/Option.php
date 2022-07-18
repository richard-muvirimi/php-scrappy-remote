<?php

/**
 * Option Model
 */

namespace App\Models;

/**
 * Option model class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Option extends BaseModel
{
	protected $table         = 'option';
	protected $allowedFields = [
		'key',
		'value',
	];
	protected $returnType    = 'App\Entities\Option';

	/**
	 * Join with meta table
	 *
	 * @param array|string $fields Fields to join.
	 * @param string       $type   Type of join.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return self
	 */
	public function joinMeta($fields, string $type = 'LEFT'):self
	{
		helper('collection');

		foreach (array_maybe($fields) as $field)
		{
			$this->join('option_meta AS ' . $field, 'option.id = ' . $field . '.option AND ' . $field . ".key = '" . $field . "'", $type);
		}

		return $this;
	}
}
