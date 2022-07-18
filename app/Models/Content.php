<?php

/**
 * Content Model
 */

namespace App\Models;

/**
 * Content model class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Content extends BaseModel
{
	protected $table         = 'content';
	protected $allowedFields = [
		'user',
		'type',
		'content',
		'parent',
	];
	protected $returnType    = 'App\Entities\Content';

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
			$this->join('content_meta AS ' . $field, 'content.id = ' . $field . '.content AND ' . $field . ".key = '" . $field . "'", $type);
		}

		return $this;
	}
}
