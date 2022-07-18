<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserMeta extends Migration
{
    public function up()
    {
        //user_meta
        $fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'user' => array(
				'type' => 'INT',
			),
			'key' => array(
				'type' => 'VARCHAR',
				'constraint'     => 50,
			),
			'value' => array(
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

		$this->forge->addKey('id', true);
		$this->forge->addField($fields);
        
        $this->forge->addForeignKey("user", "users", "id", "CASCADE", "CASCADE");
		$this->forge->addKey(["key", "user"]);

		$this->forge->createTable("user_meta", true, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->forge->dropTable('user_meta');
    }
}
