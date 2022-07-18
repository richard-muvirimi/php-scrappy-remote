<?php

/**
 * Base Model
 */

namespace App\Models;

use CodeIgniter\Model;

/**
 * Base Model Class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
abstract class BaseModel extends Model
{

	protected $useTimestamps = true;
	protected $dateFormat    = 'int';

}
