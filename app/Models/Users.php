<?php

/**
 * Users Model
 */

namespace App\Models;

/**
 * Users model class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Users extends BaseModel
{
	protected $table         = 'users';
	protected $returnType    = 'App\Entities\User';
	protected $allowedFields = [
		'email',
		'password',
		'name',
	];

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
			$this->join('user_meta AS ' . $field, 'users.id = ' . $field . '.user AND ' . $field . ".key = '" . $field . "'", $type);
		}

		return $this;
	}
}
