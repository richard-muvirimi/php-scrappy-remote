<?php

/**
 *  Option Entity
 */

namespace App\Entities;

/**
 * Option entity class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Option extends BaseEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [];

	/**
	 * Load requested meta data onto option
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

			$this->{$field} = get_option_meta($this->id, $field);

			switch($context){
				case 'view':
					if (is_object_option_meta($this->{$name}))
					{
						$this->{$name} = $this->{$name}->value;
					}
					else
					{
						$this->{$name} = '';
					}
					break;
				default:
					// Leave as is
					break;
			}
		}

		return $this;
	}
}
