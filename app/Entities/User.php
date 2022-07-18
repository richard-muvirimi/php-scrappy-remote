<?php

/**
 *  User Entity
 */

namespace App\Entities;

/**
 * User entity Class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class User extends BaseEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [];

	/**
	 * Load requested meta data onto user
	 *
	 * @param array  $fields  Fields to load.
	 * @param string $context Contextual use of value, view returns resolved value, edit the meta object.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return self
	 */
	public function with(array $fields, string $context = 'view'):self
	{
		foreach ($fields as $field => $name)
		{
			if (is_numeric($field))
			{
				$field = $name;
			}

			$this->{$field} = get_user_meta($name, $this->id);

			switch($context){
				case 'view':
					if (is_object_user_meta($this->{$field}))
					{
						$this->{$field} = $this->{$field}->value;
					}
					else
					{
						$this->{$field} = '';
					}
								break;
				default:
					// Leave as is
								break;
			}
		}

		return $this;
	}

	/**
	 * Set password to hash using password_hash function.
	 *
	 * @param string $password Password.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 * @return  self
	 */
	public function setPassword(string $password):self
	{
		$this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
		return $this;
	}

	/**
	 * Set password hash.
	 *
	 * @param string $password Password.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 * @return  self
	 */
	public function setPasswordHash(string $password):self
	{
		$this->attributes['password'] = $password;
		return $this;
	}

	/**
	 * Verify user input password with hash
	 *
	 * @param string $password Password.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 * @return  boolean
	 */
	public function verifyPassword(string $password):bool
	{
		return password_verify($password, $this->attributes['password']);
	}

}
