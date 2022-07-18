<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        //users
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'email' => array(
				'type' => 'TEXT',
			),
			'name' => array(
				'type' => 'TEXT',
			),
			'password' => array(
				'type' => 'TEXT',
			),
			'created_at' => array(
				'type'           => 'INT',
				'constraint'     => 9,
			),
			'updated_at' => array(
				'type'           => 'INT',
				'constraint'     => 9,
			),
			'deleted_at' => array(
				'type'           => 'INT',
				'constraint'     => 9,
			),
		);

		$this->forge->addField($fields);
        
		$this->forge->addPrimaryKey('id');
        
		$this->forge->createTable("users", true, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
