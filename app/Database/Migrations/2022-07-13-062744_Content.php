<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Content extends Migration
{
    public function up()
    {
        //content
        $fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'user' => array(
				'type' => 'INT',
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint'     => 50,
			),
			'content' => array(
				'type' => 'TEXT',
			),
			'parent' => array(
				'type' => 'INT',
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
        $this->forge->addKey(["user", "type", "parent"]);

		$this->forge->createTable("content", true, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->forge->dropTable('content');
    }
}
