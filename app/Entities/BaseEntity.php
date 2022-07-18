<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * Base Entity class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
abstract class BaseEntity extends Entity
{

	/**
	 * Get a string right padded to use as an id
	 *
	 * @param string  $prefix Prefix.
	 * @param integer $pad    Pad count.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return string
	 */
	public function getFancyId(string $prefix = 'SCRAPPY', int $pad = 6):string
	{
		return $prefix . str_pad($this->id, $pad, '0', STR_PAD_LEFT);
	}

}
